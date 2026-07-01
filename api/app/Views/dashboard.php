<?php

function tt_formatar_duracao(int $segundos): string
{
    $horas   = intdiv($segundos, 3600);
    $minutos = intdiv($segundos % 3600, 60);

    if ($horas === 0 && $minutos === 0) {
        return '0min';
    }

    return trim(($horas > 0 ? "{$horas}h " : '') . ($minutos > 0 ? "{$minutos}min" : ''));
}

$jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
$topApp     = $report['por_app'][0] ?? null;
$topProjeto = $report['por_projeto'][0] ?? null;
$active     = 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TimeTracker — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
  tailwind = { config: { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] } } } } };
</script>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased">
  <?= view('partials/nav', ['active' => $active]) ?>

  <main class="mx-auto max-w-5xl px-6 py-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold tracking-tight text-white">Dashboard</h1>
      <p class="mt-1 text-sm text-slate-400">Tempo registrado por app/domínio, projeto e dia.</p>
    </div>

    <form method="get" class="mb-8 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-800 bg-slate-900/50 p-5">
      <div>
        <label for="de" class="mb-1 block text-xs font-medium text-slate-400">De</label>
        <input type="date" id="de" name="de" value="<?= esc($filtros['de'] ?? '') ?>"
               class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
      </div>
      <div>
        <label for="ate" class="mb-1 block text-xs font-medium text-slate-400">Até</label>
        <input type="date" id="ate" name="ate" value="<?= esc($filtros['ate'] ?? '') ?>"
               class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
      </div>
      <div>
        <label for="projeto_id" class="mb-1 block text-xs font-medium text-slate-400">Projeto</label>
        <select id="projeto_id" name="projeto_id"
                class="rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
          <option value="">Todos</option>
          <?php foreach ($projetos as $projeto) : ?>
            <option value="<?= esc($projeto['id']) ?>" <?= $filtros['projeto_id'] === (int) $projeto['id'] ? 'selected' : '' ?>>
              <?= esc($projeto['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="rounded-lg bg-indigo-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-400">
        Filtrar
      </button>
      <?php if ($filtros['de'] || $filtros['ate'] || $filtros['projeto_id']) : ?>
        <a href="<?= site_url('/') ?>" class="text-sm text-slate-400 transition hover:text-slate-200">Limpar filtros</a>
      <?php endif; ?>
    </form>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
      <div class="rounded-2xl border border-slate-800 bg-gradient-to-br from-indigo-500/15 to-transparent p-6">
        <div class="text-3xl font-extrabold text-indigo-400"><?= tt_formatar_duracao($report['total_seg']) ?></div>
        <div class="mt-1 text-sm text-slate-400">tempo total no período</div>
      </div>
      <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
        <div class="truncate text-xl font-semibold text-white"><?= $topApp ? esc($topApp['app_ou_dominio']) : '—' ?></div>
        <div class="mt-1 text-sm text-slate-400">app/domínio principal</div>
      </div>
      <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
        <div class="truncate text-xl font-semibold text-white"><?= $topProjeto ? esc($topProjeto['nome'] ?? 'Sem projeto') : '—' ?></div>
        <div class="mt-1 text-sm text-slate-400">projeto principal</div>
      </div>
    </div>

    <?php if ($report['total_seg'] === 0) : ?>
      <div class="rounded-2xl border border-dashed border-slate-800 py-16 text-center text-sm text-slate-500">
        Nenhum registro no período selecionado.
      </div>
    <?php else : ?>
      <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
          <h2 class="mb-4 text-sm font-semibold text-slate-300">Por app / domínio</h2>
          <canvas id="chart-app"></canvas>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
          <h2 class="mb-4 text-sm font-semibold text-slate-300">Por projeto</h2>
          <canvas id="chart-projeto"></canvas>
        </div>
        <div class="rounded-2xl border border-slate-800 bg-slate-900/50 p-6 md:col-span-2">
          <h2 class="mb-4 text-sm font-semibold text-slate-300">Por dia</h2>
          <canvas id="chart-dia"></canvas>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    const porApp = <?= json_encode($report['por_app'], $jsonFlags) ?>;
    const porProjeto = <?= json_encode($report['por_projeto'], $jsonFlags) ?>;
    const porDia = <?= json_encode($report['por_dia'], $jsonFlags) ?>;

    const paleta = ['#818cf8', '#fbbf24', '#34d399', '#fb7185', '#a78bfa', '#22d3ee', '#f472b6', '#facc15'];
    const horas = (segundos) => +(segundos / 3600).toFixed(2);
    const truncar = (texto, max = 22) => (texto.length > max ? `${texto.slice(0, max - 1)}…` : texto);

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.15)';
    Chart.defaults.font.family = "'Inter', ui-sans-serif, system-ui";

    // Espera a fonte Inter carregar antes de desenhar (evita medir a
    // largura dos labels com a fonte de fallback errada).
    document.fonts.ready.then(() => {
      if (porApp.length > 0) {
        new Chart(document.getElementById('chart-app'), {
          type: 'bar',
          data: {
            labels: porApp.map((r) => r.app_ou_dominio),
            datasets: [{
              label: 'Horas',
              data: porApp.map((r) => horas(r.duracao_seg)),
              backgroundColor: '#818cf8',
              borderRadius: 6,
            }],
          },
          options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
              x: { title: { display: true, text: 'Horas' }, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
              y: {
                grid: { display: false },
                ticks: {
                  crossAlign: 'start',
                  callback: (_value, index) => truncar(porApp[index].app_ou_dominio),
                },
              },
            },
          },
        });
      }

      if (porProjeto.length > 0) {
        new Chart(document.getElementById('chart-projeto'), {
          type: 'doughnut',
          data: {
            labels: porProjeto.map((r) => r.nome ?? 'Sem projeto'),
            datasets: [{
              data: porProjeto.map((r) => horas(r.duracao_seg)),
              backgroundColor: porProjeto.map((r, i) => r.cor ?? paleta[i % paleta.length]),
              borderColor: '#0f172a',
              borderWidth: 2,
            }],
          },
          options: {
            plugins: { legend: { position: 'bottom' } },
          },
        });
      }

      if (porDia.length > 0) {
        new Chart(document.getElementById('chart-dia'), {
          type: 'line',
          data: {
            labels: porDia.map((r) => r.dia),
            datasets: [{
              label: 'Horas por dia',
              data: porDia.map((r) => horas(r.duracao_seg)),
              borderColor: '#818cf8',
              backgroundColor: 'rgba(129, 140, 248, 0.15)',
              fill: true,
              tension: 0.3,
              pointBackgroundColor: '#818cf8',
            }],
          },
          options: {
            plugins: { legend: { display: false } },
            scales: {
              y: {
                beginAtZero: true,
                title: { display: true, text: 'Horas' },
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
              },
              x: { grid: { display: false } },
            },
          },
        });
      }
    });
  </script>
</body>
</html>
