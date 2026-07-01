<?php $active = 'projetos'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <?= view('partials/head', ['title' => 'Cronos — Projetos']) ?>
</head>
<body class="min-h-screen font-sans text-slate-100 antialiased">
  <?= view('partials/nav', ['active' => $active]) ?>

  <main class="relative z-10 mx-auto max-w-4xl px-5 pb-16 pt-10 sm:px-8 sm:pt-14">
    <div class="mb-8">
      <div class="eyebrow mb-4 flex items-center gap-2 text-[10px] font-bold text-lime">
        <span class="h-1.5 w-1.5 rounded-full bg-lime shadow-[0_0_12px_#c6ff4a]"></span>
        Projetos
      </div>
      <h1 class="font-display text-3xl font-extrabold tracking-[-.05em] text-white sm:text-4xl">Organize seu tempo por projeto.</h1>
    </div>

    <?php if (! empty($success)) : ?>
      <div class="mb-5 rounded-2xl border border-lime/20 bg-lime/[.08] px-4 py-4 text-sm text-[#dcffa0]" role="status"><?= esc($success) ?></div>
    <?php endif; ?>

    <?php if (! empty($errors)) : ?>
      <div class="mb-5 rounded-2xl border border-coral/20 bg-coral/[.08] px-5 py-4 text-sm text-[#ffaaa2]" role="alert">
        <ul class="space-y-1 pl-5">
          <?php foreach ($errors as $error) : ?><li class="list-disc"><?= esc($error) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('projetos') ?>" class="glass mb-8 grid gap-4 rounded-[24px] p-5 sm:grid-cols-[1fr_auto_auto] sm:items-end">
      <?= csrf_field() ?>
      <div>
        <label for="nome" class="mb-2 block text-xs font-bold text-slate-300">Nome do projeto <span class="text-lime">*</span></label>
        <input type="text" id="nome" name="nome" value="<?= esc(old('nome')) ?>" maxlength="100" required class="field" placeholder="Ex.: Cronos Track">
      </div>
      <div>
        <label for="cor" class="mb-2 block text-xs font-bold text-slate-300">Cor</label>
        <input type="color" id="cor" name="cor" value="<?= esc(old('cor') ?: '#888888') ?>" class="field h-[46px] w-16 cursor-pointer p-1">
      </div>
      <button type="submit" class="h-[46px] rounded-2xl bg-lime px-6 text-sm font-bold text-ink transition hover:-translate-y-0.5 hover:bg-[#d5ff76]">Criar projeto</button>
    </form>

    <div class="glass rounded-[24px] p-2">
      <?php if (empty($projetos)) : ?>
        <p class="px-5 py-8 text-center text-sm text-slate-500">Nenhum projeto cadastrado ainda.</p>
      <?php else : ?>
        <ul class="divide-y divide-white/[.06]">
          <?php foreach ($projetos as $projeto) : ?>
            <li class="flex items-center gap-3 px-5 py-4">
              <span class="h-3 w-3 shrink-0 rounded-full" style="background: <?= esc($projeto['cor']) ?>"></span>
              <span class="font-semibold text-slate-200"><?= esc($projeto['nome']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
