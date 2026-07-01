const $ = (id) => document.getElementById(id);

async function loadConfig() {
  const { apiBaseUrl = "http://127.0.0.1:8080", apiKey = "", captureFullUrl = false } =
    await chrome.storage.local.get(["apiBaseUrl", "apiKey", "captureFullUrl"]);

  $("apiBaseUrl").value = apiBaseUrl;
  $("apiKey").value = apiKey;
  $("captureFullUrl").checked = captureFullUrl;
}

async function loadStatus() {
  const { current } = await chrome.storage.session.get("current");
  const { pending = [] } = await chrome.storage.local.get("pending");

  $("status").textContent = current
    ? `Rastreando: ${current.value} (pendentes: ${pending.length})`
    : `Sem sessão ativa (pendentes: ${pending.length})`;
}

$("save").addEventListener("click", async () => {
  await chrome.storage.local.set({
    apiBaseUrl: $("apiBaseUrl").value.trim().replace(/\/+$/, ""),
    apiKey: $("apiKey").value.trim(),
    captureFullUrl: $("captureFullUrl").checked,
  });

  $("saved").style.display = "block";
  setTimeout(() => ($("saved").style.display = "none"), 1500);
});

loadConfig();
loadStatus();
