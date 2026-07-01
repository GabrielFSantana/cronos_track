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

$jsonFlags  = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
$topApp     = $report['por_app'][0] ?? null;
$topProjeto = $report['por_projeto'][0] ?? null;
$active     = 'dashboard';
$temFiltros = $filtros['de'] || $filtros['ate'] || $filtros['projeto_id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= view('partials/head', ['title' => 'Cronos — Visão geral']) ?>
</head>
<body class="min-h-screen font-sans text-slate-100 antialiased">
  <?= view('partials/nav', ['active' => $active]) ?>

  <main class="relative z-10 mx-auto max-w-7xl px-5 pb-16 pt-10 sm:px-8 sm:pt-14">
    <img src="<?= base_url('assets/cronos-logo.png') ?>" alt="" class="pointer-events-none absolute -right-24 -top-24 -z-10 hidden w-[430px] select-none opacity-[.045] mix-blend-screen lg:block" aria-hidden="true">
    <section class="reveal mb-10 flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
      <div class="max-w-2xl">
        <div class="eyebrow mb-4 flex items-center gap-2 text-[10px] font-bold text-lime">
          <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-lime shadow-[0_0_12px_#c6ff4a]"></span>
          Painel de produtividade
        </div>
        <h1 class="font-display text-4xl font-extrabold leading-[1.05] tracking-[-.055em] text-white sm:text-5xl lg:text-[58px]">
          Seu tempo, finalmente<br class="hidden sm:block">
          <span class="bg-gradient-to-r from-lime via-[#e8ffb5] to-violet bg-clip-text text-transparent">trabalhando a seu favor.</span>
        </h1>
        <p class="mt-5 max-w-xl text-[15px] leading-7 text-slate-400">
          Transforme cada hora registrada em clareza para decidir melhor, criar mais e perder menos tempo.
        </p>
      </div>
      <a href="<?= site_url('lancamento') ?>" class="group inline-flex w-fit items-center gap-3 rounded-2xl bg-lime px-5 py-3.5 text-sm font-bold text-ink shadow-glow transition hover:-translate-y-0.5 hover:bg-[#d5ff76]">
        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-ink/10">
          <svg class="h-4 w-4 transition-transform group-hover:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
          </svg>
        </span>
        Registrar atividade
      </a>
    </section>

    <form method="get" class="glass reveal reveal-delay-1 mb-5 grid gap-3 rounded-[22px] p-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1.3fr_auto]" aria-label="Filtros do relatório">
      <label class="group rounded-2xl px-3 py-2 transition hover:bg-white/[.025]" for="de">
        <span class="eyebrow mb-1.5 block text-[9px] font-bold text-slate-500">Período inicial</span>
        <input type="date" id="de" name="de" value="<?= esc($filtros['de'] ?? '') ?>" class="w-full bg-transparent text-sm font-semibold text-slate-200 outline-none">
      </label>
      <label class="group rounded-2xl px-3 py-2 transition hover:bg-white/[.025]" for="ate">
        <span class="eyebrow mb-1.5 block text-[9px] font-bold text-slate-500">Período final</span>
        <input type="date" id="ate" name="ate" value="<?= esc($filtros['ate'] ?? '') ?>" class="w-full bg-transparent text-sm font-semibold text-slate-200 outline-none">
      </label>
      <label class="rounded-2xl px-3 py-2 transition hover:bg-white/[.025]" for="projeto_id">
        <span class="eyebrow mb-1.5 block text-[9px] font-bold text-slate-500">Projeto</span>
        <select id="projeto_id" name="projeto_id" class="w-full cursor-pointer bg-transparent text-sm font-semibold text-slate-200 outline-none">
          <option class="bg-slate-900" value="">Todos os projetos</option>
          <?php foreach ($projetos as $projeto) : ?>
            <option class="bg-slate-900" value="<?= esc($projeto['id']) ?>" <?= $filtros['projeto_id'] === (int) $projeto['id'] ? 'selected' : '' ?>>
              <?= esc($projeto['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <div class="flex items-center gap-2">
        <?php if ($temFiltros) : ?>
          <a href="<?= site_url('/') ?>" class="rounded-xl p-3 text-slate-500 transition hover:bg-white/[.06] hover:text-white" aria-label="Limpar filtros" title="Limpar filtros">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
          </a>
        <?php endif; ?>
        <button type="submit" class="h-full min-h-12 w-full rounded-2xl bg-white px-6 text-sm font-bold text-ink transition hover:bg-lime lg:w-auto">
          Aplicar
        </button>
      </div>
    </form>

    <section class="reveal reveal-delay-2 mb-5 grid auto-rows-[190px] gap-5 lg:grid-cols-12" aria-label="Resumo do período">
      <article class="relative overflow-hidden rounded-[28px] bg-lime p-7 text-ink shadow-glow lg:col-span-5">
        <div class="absolute -right-12 -top-12 h-44 w-44 rounded-full border-[28px] border-ink/[.055]"></div>
        <div class="absolute bottom-0 right-8 h-20 w-20 rounded-t-full bg-ink/[.045]"></div>
        <div class="relative flex h-full flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="eyebrow text-[10px] font-extrabold text-ink/55">Tempo em foco</span>
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink/10">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </span>
          </div>
          <div>
            <div class="font-display text-[46px] font-extrabold leading-none tracking-[-.06em] sm:text-[54px]"><?= tt_formatar_duracao($report['total_seg']) ?></div>
            <p class="mt-2 text-sm font-semibold text-ink/55">registrados no período</p>
          </div>
        </div>
      </article>

      <article class="glass group rounded-[28px] p-7 transition duration-300 hover:-translate-y-1 hover:border-violet/30 lg:col-span-4">
        <div class="flex h-full flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="eyebrow text-[10px] font-bold text-slate-500">Mais utilizado</span>
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-violet/15 text-violet">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 6.5h.01M11 6.5h.01"/></svg>
            </span>
          </div>
          <div>
            <div class="truncate font-display text-2xl font-bold tracking-[-.04em] text-white"><?= $topApp ? esc($topApp['app_ou_dominio']) : 'Sem dados' ?></div>
            <p class="mt-2 text-sm text-slate-500"><?= $topApp ? tt_formatar_duracao((int) $topApp['duracao_seg']) . ' de atividade' : 'Registre uma atividade para começar' ?></p>
          </div>
        </div>
      </article>

      <article class="glass group rounded-[28px] p-7 transition duration-300 hover:-translate-y-1 hover:border-coral/30 lg:col-span-3">
        <div class="flex h-full flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="eyebrow text-[10px] font-bold text-slate-500">Projeto líder</span>
            <span class="h-3 w-3 rounded-full shadow-[0_0_16px_currentColor]" style="color: <?= esc($topProjeto['cor'] ?? '#ff7163') ?>; background: currentColor"></span>
          </div>
          <div>
            <div class="truncate font-display text-2xl font-bold tracking-[-.04em] text-white"><?= $topProjeto ? esc($topProjeto['nome'] ?? 'Sem projeto') : 'Sem dados' ?></div>
            <p class="mt-2 text-sm text-slate-500"><?= $topProjeto ? tt_formatar_duracao((int) $topProjeto['duracao_seg']) . ' investidos' : 'Seus projetos aparecerão aqui' ?></p>
          </div>
        </div>
      </article>
    </section>

    <?php if ($report['total_seg'] === 0) : ?>
      <section class="glass relative overflow-hidden rounded-[30px] px-6 py-16 text-center sm:py-20">
        <div class="absolute left-1/2 top-1/2 h-52 w-52 -translate-x-1/2 -translate-y-1/2 rounded-full bg-violet/10 blur-3xl"></div>
        <div class="relative mx-auto max-w-md">
          <span class="brand-mark mx-auto block h-16 w-16 rounded-[22px] border border-white/10 bg-ink shadow-card" aria-hidden="true"></span>
          <h2 class="mt-6 font-display text-2xl font-bold tracking-[-.04em] text-white">Seu próximo insight começa agora</h2>
          <p class="mt-3 text-sm leading-6 text-slate-500">Não encontramos registros neste período. Adicione a primeira atividade e veja seu tempo ganhar forma.</p>
          <a href="<?= site_url('lancamento') ?>" class="mt-7 inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/[.06] px-5 py-3 text-sm font-bold text-white transition hover:border-lime/30 hover:text-lime">
            Criar primeiro registro <span aria-hidden="true">→</span>
          </a>
        </div>
      </section>
    <?php else : ?>
      <section class="grid gap-5 lg:grid-cols-12">
        <article class="glass rounded-[28px] p-6 sm:p-7 lg:col-span-7">
          <div class="mb-7 flex items-center justify-between">
            <div><span class="eyebrow text-[9px] font-bold text-lime">Distribuição</span><h2 class="mt-1 font-display text-xl font-bold tracking-[-.035em] text-white">Apps e domínios</h2></div>
            <span class="rounded-full bg-white/[.05] px-3 py-1.5 text-[10px] font-bold text-slate-500">TOP <?= min(count($report['por_app']), 8) ?></span>
          </div>
          <div class="h-[330px]"><canvas id="chart-app"></canvas></div>
        </article>

        <article class="glass rounded-[28px] p-6 sm:p-7 lg:col-span-5">
          <div class="mb-7"><span class="eyebrow text-[9px] font-bold text-violet">Participação</span><h2 class="mt-1 font-display text-xl font-bold tracking-[-.035em] text-white">Tempo por projeto</h2></div>
          <div class="h-[330px]"><canvas id="chart-projeto"></canvas></div>
        </article>

        <article class="glass rounded-[28px] p-6 sm:p-7 lg:col-span-12">
          <div class="mb-7 flex items-end justify-between"><div><span class="eyebrow text-[9px] font-bold text-coral">Ritmo</span><h2 class="mt-1 font-display text-xl font-bold tracking-[-.035em] text-white">Evolução diária</h2></div><span class="hidden text-xs text-slate-600 sm:block">horas registradas por dia</span></div>
          <div class="h-[290px]"><canvas id="chart-dia"></canvas></div>
        </article>
      </section>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    const porApp = <?= json_encode($report['por_app'], $jsonFlags) ?>;
    const porProjeto = <?= json_encode($report['por_projeto'], $jsonFlags) ?>;
    const porDia = <?= json_encode($report['por_dia'], $jsonFlags) ?>;
    const paleta = ['#c6ff4a', '#8b7cff', '#ff7163', '#50d5ff', '#ffcd58', '#ef7dff', '#5ce1a9', '#ff9f6e'];
    const horas = (segundos) => +(segundos / 3600).toFixed(2);
    const truncar = (texto, max = 24) => texto.length > max ? `${texto.slice(0, max - 1)}…` : texto;
    const tooltip = {
      backgroundColor: '#121722', titleColor: '#fff', bodyColor: '#94a3b8', padding: 14,
      cornerRadius: 12, borderColor: 'rgba(255,255,255,.08)', borderWidth: 1, displayColors: false,
      callbacks: { label: (context) => `${context.parsed.x ?? context.parsed ?? 0} horas` },
    };

    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(255,255,255,.055)';
    Chart.defaults.font.family = "'DM Sans', ui-sans-serif, system-ui";

    document.fonts.ready.then(() => {
      if (porApp.length) new Chart(document.getElementById('chart-app'), {
        type: 'bar',
        data: { labels: porApp.slice(0, 8).map(r => r.app_ou_dominio), datasets: [{ data: porApp.slice(0, 8).map(r => horas(r.duracao_seg)), backgroundColor: porApp.slice(0, 8).map((_, i) => i === 0 ? '#c6ff4a' : 'rgba(255,255,255,.1)'), borderRadius: 8, borderSkipped: false, barThickness: 13 }] },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false }, tooltip }, scales: { x: { beginAtZero: true, ticks: { padding: 8 }, grid: { drawBorder: false } }, y: { grid: { display: false }, ticks: { color: '#cbd5e1', callback: (_v, i) => truncar(porApp[i].app_ou_dominio) } } } },
      });

      if (porProjeto.length) new Chart(document.getElementById('chart-projeto'), {
        type: 'doughnut',
        data: { labels: porProjeto.map(r => r.nome ?? 'Sem projeto'), datasets: [{ data: porProjeto.map(r => horas(r.duracao_seg)), backgroundColor: porProjeto.map((r, i) => r.cor ?? paleta[i % paleta.length]), borderColor: '#10141d', borderWidth: 5, hoverOffset: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', padding: 18, boxWidth: 7, color: '#94a3b8' } }, tooltip: { ...tooltip, callbacks: { label: c => `${c.label}: ${c.parsed} horas` } } } },
      });

      if (porDia.length) {
        const ctx = document.getElementById('chart-dia').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 290); gradient.addColorStop(0, 'rgba(198,255,74,.22)'); gradient.addColorStop(1, 'rgba(198,255,74,0)');
        new Chart(ctx, { type: 'line', data: { labels: porDia.map(r => r.dia), datasets: [{ data: porDia.map(r => horas(r.duracao_seg)), borderColor: '#c6ff4a', backgroundColor: gradient, fill: true, tension: .42, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#c6ff4a', pointHoverBorderColor: '#080b12', pointHoverBorderWidth: 3 }] }, options: { responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, plugins: { legend: { display: false }, tooltip: { ...tooltip, callbacks: { label: c => `${c.parsed.y} horas` } } }, scales: { y: { beginAtZero: true, grid: { drawBorder: false }, ticks: { padding: 12 } }, x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkipPadding: 24 } } } } });
      }
    });
  </script>
</body>
</html>
