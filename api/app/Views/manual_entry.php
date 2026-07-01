<?php $active = 'lancamento'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= view('partials/head', ['title' => 'Cronos — Novo registro']) ?>
</head>
<body class="min-h-screen font-sans text-slate-100 antialiased">
  <?= view('partials/nav', ['active' => $active]) ?>

  <main class="relative z-10 mx-auto max-w-7xl px-5 pb-16 pt-10 sm:px-8 sm:pt-14">
    <div class="grid gap-10 lg:grid-cols-[.82fr_1.18fr] lg:gap-16">
      <section class="reveal flex flex-col justify-between lg:sticky lg:top-32 lg:h-[640px]">
        <div>
          <div class="eyebrow mb-4 flex items-center gap-2 text-[10px] font-bold text-lime">
            <span class="h-1.5 w-1.5 rounded-full bg-lime shadow-[0_0_12px_#c6ff4a]"></span>
            Entrada manual
          </div>
          <h1 class="max-w-xl font-display text-4xl font-extrabold leading-[1.07] tracking-[-.055em] text-white sm:text-5xl">
            Dê contexto ao<br><span class="text-lime">tempo que importa.</span>
          </h1>
          <p class="mt-5 max-w-md text-[15px] leading-7 text-slate-400">
            Para reuniões, estudos ou qualquer atividade que escapou da captura automática. Rápido, preciso e organizado.
          </p>
        </div>

        <div class="relative mt-10 hidden overflow-hidden rounded-[30px] border border-white/[.08] bg-gradient-to-br from-violet/20 via-white/[.035] to-transparent p-7 lg:block">
          <div class="absolute -right-12 -top-14 h-40 w-40 rounded-full bg-violet/15 blur-3xl"></div>
          <div class="relative">
            <div class="mb-8 flex items-center justify-between">
              <span class="eyebrow text-[9px] font-bold text-slate-500">Duração calculada</span>
              <span class="brand-mark h-9 w-9 rounded-xl border border-white/10 bg-ink" aria-hidden="true"></span>
            </div>
            <div id="duration-display" class="font-display text-5xl font-extrabold tracking-[-.06em] text-white">—</div>
            <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-white/[.07]"><div id="duration-bar" class="h-full w-0 rounded-full bg-gradient-to-r from-violet to-lime transition-all duration-500"></div></div>
            <p id="duration-caption" class="mt-3 text-xs text-slate-500">Preencha o início e o fim</p>
          </div>
        </div>

        <p class="mt-8 hidden items-center gap-2 text-xs text-slate-600 lg:flex">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
          Os registros ficam disponíveis imediatamente no painel.
        </p>
      </section>

      <section class="reveal reveal-delay-1">
        <?php if (! empty($success)) : ?>
          <div class="mb-5 flex items-start gap-3 rounded-2xl border border-lime/20 bg-lime/[.08] px-4 py-4 text-sm text-[#dcffa0]" role="status">
            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-lime text-ink">
              <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/></svg>
            </span>
            <?= esc($success) ?>
          </div>
        <?php endif; ?>

        <?php if (! empty($errors)) : ?>
          <div class="mb-5 rounded-2xl border border-coral/20 bg-coral/[.08] px-5 py-4 text-sm text-[#ffaaa2]" role="alert">
            <div class="mb-2 flex items-center gap-2 font-bold text-[#ffc2bc]">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.8 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg>
              Revise os campos abaixo
            </div>
            <ul class="space-y-1 pl-6 text-xs text-coral/80">
              <?php foreach ($errors as $error) : ?><li class="list-disc"><?= esc($error) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('lancamento') ?>" class="glass rounded-[30px] p-5 sm:p-8" id="manual-form">
          <?= csrf_field() ?>

          <div class="mb-8 flex items-center justify-between border-b border-white/[.07] pb-6">
            <div>
              <span class="eyebrow text-[9px] font-bold text-slate-500">Novo bloco de tempo</span>
              <h2 class="mt-1 font-display text-xl font-bold tracking-[-.035em] text-white">Detalhes da atividade</h2>
            </div>
            <span class="rounded-full border border-white/[.08] bg-white/[.035] px-3 py-1.5 text-[9px] font-bold uppercase tracking-wider text-slate-500">* obrigatório</span>
          </div>

          <div class="space-y-6">
            <div>
              <label for="app_ou_dominio" class="mb-2.5 flex items-center justify-between text-xs font-bold text-slate-300">
                <span>O que você fez? <span class="text-lime">*</span></span>
                <span class="font-normal text-slate-600">máx. 255</span>
              </label>
              <input type="text" id="app_ou_dominio" name="app_ou_dominio" value="<?= esc(old('app_ou_dominio')) ?>" maxlength="255" required class="field" placeholder="Ex.: Reunião de planejamento">
            </div>

            <div>
              <label for="titulo" class="mb-2.5 block text-xs font-bold text-slate-300">Adicione um contexto <span class="font-normal text-slate-600">(opcional)</span></label>
              <textarea id="titulo" name="titulo" maxlength="500" rows="3" class="field resize-none" placeholder="Uma breve descrição do que foi realizado..."><?= esc(old('titulo')) ?></textarea>
            </div>

            <div>
              <label for="projeto_id" class="mb-2.5 block text-xs font-bold text-slate-300">Vincular a um projeto <span class="font-normal text-slate-600">(opcional)</span></label>
              <div class="relative">
                <select id="projeto_id" name="projeto_id" class="field cursor-pointer appearance-none pr-11">
                  <option value="">Nenhum projeto</option>
                  <?php foreach ($projetos as $projeto) : ?>
                    <option value="<?= esc($projeto['id']) ?>" <?= (string) old('projeto_id') === (string) $projeto['id'] ? 'selected' : '' ?>><?= esc($projeto['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="inicio" class="mb-2.5 block text-xs font-bold text-slate-300">Início <span class="text-lime">*</span></label>
                <input type="datetime-local" id="inicio" name="inicio" value="<?= esc(old('inicio')) ?>" required class="field text-sm">
              </div>
              <div>
                <label for="fim" class="mb-2.5 block text-xs font-bold text-slate-300">Fim <span class="text-lime">*</span></label>
                <input type="datetime-local" id="fim" name="fim" value="<?= esc(old('fim')) ?>" required class="field text-sm">
              </div>
            </div>
          </div>

          <div class="mt-8 flex flex-col-reverse gap-3 border-t border-white/[.07] pt-6 sm:flex-row sm:items-center sm:justify-between">
            <a href="<?= site_url('/') ?>" class="rounded-xl px-3 py-3 text-center text-sm font-semibold text-slate-500 transition hover:text-white">Cancelar</a>
            <button type="submit" class="group inline-flex items-center justify-center gap-3 rounded-2xl bg-lime px-6 py-3.5 text-sm font-bold text-ink shadow-glow transition hover:-translate-y-0.5 hover:bg-[#d5ff76]">
              Salvar registro
              <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
            </button>
          </div>
        </form>
      </section>
    </div>
  </main>

  <script>
    const inicio = document.getElementById('inicio');
    const fim = document.getElementById('fim');
    const display = document.getElementById('duration-display');
    const caption = document.getElementById('duration-caption');
    const bar = document.getElementById('duration-bar');

    function atualizarDuracao() {
      if (!inicio.value || !fim.value) {
        display.textContent = '—'; caption.textContent = 'Preencha o início e o fim'; bar.style.width = '0'; return;
      }
      const minutos = Math.round((new Date(fim.value) - new Date(inicio.value)) / 60000);
      if (minutos <= 0) {
        display.textContent = 'Inválido'; caption.textContent = 'O fim precisa ser posterior ao início'; bar.style.width = '8%'; return;
      }
      const horas = Math.floor(minutos / 60); const resto = minutos % 60;
      display.textContent = `${horas ? `${horas}h ` : ''}${resto ? `${resto}min` : ''}`;
      caption.textContent = minutos < 60 ? 'Um bloco curto e focado' : `${(minutos / 60).toFixed(1).replace('.', ',')} horas registradas`;
      bar.style.width = `${Math.min(100, Math.max(12, (minutos / 480) * 100))}%`;
    }
    inicio.addEventListener('change', atualizarDuracao);
    fim.addEventListener('change', atualizarDuracao);
    atualizarDuracao();
  </script>
</body>
</html>
