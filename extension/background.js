// Service worker (Manifest V3). Rastreia a aba ativa e envia sessões
// (início/fim) para a API quando a aba/domínio muda — nunca uma linha
// por polling. Roda independente do agente Windows.

const DEFAULT_CONFIG = {
  apiBaseUrl: "http://127.0.0.1:8080",
  apiKey: "",
  captureFullUrl: false,
};

const FLUSH_ALARM = "timetracker-flush";
const FLUSH_PERIOD_MINUTES = 1;

function pad(n) {
  return String(n).padStart(2, "0");
}

function formatDateTime(date) {
  return (
    `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ` +
    `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
  );
}

function extractValue(url, captureFullUrl) {
  try {
    const parsed = new URL(url);
    if (parsed.protocol !== "http:" && parsed.protocol !== "https:") {
      return null;
    }
    return captureFullUrl ? url : parsed.hostname;
  } catch {
    return null;
  }
}

async function getConfig() {
  const stored = await chrome.storage.local.get(DEFAULT_CONFIG);
  return { ...DEFAULT_CONFIG, ...stored };
}

async function getCurrent() {
  const { current } = await chrome.storage.session.get("current");
  return current ?? null;
}

async function setCurrent(current) {
  await chrome.storage.session.set({ current });
}

async function appendPending(record) {
  const { pending = [] } = await chrome.storage.local.get("pending");
  pending.push(record);
  await chrome.storage.local.set({ pending });
}

async function closeCurrentSession(fimDate) {
  const current = await getCurrent();
  if (!current) return;

  const inicio = new Date(current.inicioIso);
  const fim = fimDate ?? new Date();

  if (fim <= inicio) {
    await setCurrent(null);
    return;
  }

  await appendPending({
    origem: "navegador",
    app_ou_dominio: current.value,
    titulo: current.titulo || null,
    inicio: formatDateTime(inicio),
    fim: formatDateTime(fim),
  });

  await setCurrent(null);
}

async function startSession(value, titulo, tabId) {
  await setCurrent({ value, titulo: titulo || null, tabId, inicioIso: new Date().toISOString() });
}

async function handleTab(tab) {
  const config = await getConfig();
  const value = tab && tab.url ? extractValue(tab.url, config.captureFullUrl) : null;

  if (!value) {
    await closeCurrentSession();
    return;
  }

  const current = await getCurrent();

  if (!current) {
    await startSession(value, tab.title, tab.id);
    return;
  }

  if (current.value !== value) {
    await closeCurrentSession();
    await startSession(value, tab.title, tab.id);
    return;
  }

  // Mesma aba/domínio ainda ativa: só atualiza o título e a aba mais recente.
  await setCurrent({ ...current, titulo: tab.title || current.titulo, tabId: tab.id });
}

async function handleActiveTabInWindow(windowId) {
  try {
    const [tab] = await chrome.tabs.query({ active: true, windowId });
    await handleTab(tab ?? null);
  } catch {
    await closeCurrentSession();
  }
}

async function flushPending() {
  const config = await getConfig();
  const { pending = [] } = await chrome.storage.local.get("pending");

  if (pending.length === 0 || !config.apiKey) return;

  try {
    const response = await fetch(`${config.apiBaseUrl}/api/activities`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-API-Key": config.apiKey,
      },
      body: JSON.stringify({ activities: pending }),
    });

    if (response.ok) {
      await chrome.storage.local.set({ pending: [] });
    }
  } catch {
    // Sem conexão com a API agora — tenta de novo no próximo alarme.
  }
}

chrome.tabs.onActivated.addListener(async ({ tabId }) => {
  try {
    const tab = await chrome.tabs.get(tabId);
    await handleTab(tab);
  } catch {
    await closeCurrentSession();
  }
});

chrome.tabs.onUpdated.addListener(async (tabId, changeInfo, tab) => {
  if (!changeInfo.url || !tab.active) return;
  await handleTab(tab);
});

chrome.windows.onFocusChanged.addListener(async (windowId) => {
  if (windowId === chrome.windows.WINDOW_ID_NONE) {
    // Nenhuma janela em foco (ex.: navegador inteiro fechado): aplica na hora.
    await closeCurrentSession();
    await flushPending();
    return;
  }
  await handleActiveTabInWindow(windowId);
});

chrome.tabs.onRemoved.addListener(async (tabId) => {
  const current = await getCurrent();
  if (!current || current.tabId !== tabId) return;

  // A aba rastreada foi fechada: encerra a sessão e envia pra API na hora,
  // sem esperar o próximo alarme (até 1 min de atraso).
  await closeCurrentSession();
  await flushPending();
});

chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name === FLUSH_ALARM) {
    flushPending();
  }
});

chrome.runtime.onInstalled.addListener(() => {
  chrome.alarms.create(FLUSH_ALARM, { periodInMinutes: FLUSH_PERIOD_MINUTES });
});

chrome.runtime.onStartup.addListener(() => {
  chrome.alarms.create(FLUSH_ALARM, { periodInMinutes: FLUSH_PERIOD_MINUTES });
});
