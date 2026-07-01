<?php $active = 'lancamento'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TimeTracker — Lançamento manual</title>
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

  <main class="mx-auto max-w-lg px-6 py-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold tracking-tight text-white">Lançamento manual</h1>
      <p class="mt-1 text-sm text-slate-400">Registrar um bloco de tempo que não foi capturado automaticamente.</p>
    </div>

    <?php if (! empty($success)) : ?>
      <div class="mb-6 rounded-xl border border-emerald-800 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300">
        <?= esc($success) ?>
      </div>
    <?php endif; ?>

    <?php if (! empty($errors)) : ?>
      <div class="mb-6 space-y-1 rounded-xl border border-rose-800 bg-rose-950/50 px-4 py-3 text-sm text-rose-300">
        <?php foreach ($errors as $error) : ?>
          <div><?= esc($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('lancamento') ?>"
          class="space-y-5 rounded-2xl border border-slate-800 bg-slate-900/50 p-6">
      <?= csrf_field() ?>

      <div>
        <label for="app_ou_dominio" class="mb-1 block text-xs font-medium text-slate-400">Descrição *</label>
        <input type="text" id="app_ou_dominio" name="app_ou_dominio" value="<?= esc(old('app_ou_dominio')) ?>" maxlength="255" required
               class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
      </div>

      <div>
        <label for="titulo" class="mb-1 block text-xs font-medium text-slate-400">Detalhe (opcional)</label>
        <input type="text" id="titulo" name="titulo" value="<?= esc(old('titulo')) ?>" maxlength="500"
               class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
      </div>

      <div>
        <label for="projeto_id" class="mb-1 block text-xs font-medium text-slate-400">Projeto (opcional)</label>
        <select id="projeto_id" name="projeto_id"
                class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
          <option value="">— nenhum —</option>
          <?php foreach ($projetos as $projeto) : ?>
            <option value="<?= esc($projeto['id']) ?>" <?= (string) old('projeto_id') === (string) $projeto['id'] ? 'selected' : '' ?>>
              <?= esc($projeto['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex gap-4">
        <div class="flex-1">
          <label for="inicio" class="mb-1 block text-xs font-medium text-slate-400">Início *</label>
          <input type="datetime-local" id="inicio" name="inicio" value="<?= esc(old('inicio')) ?>" required
                 class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div class="flex-1">
          <label for="fim" class="mb-1 block text-xs font-medium text-slate-400">Fim *</label>
          <input type="datetime-local" id="fim" name="fim" value="<?= esc(old('fim')) ?>" required
                 class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2 text-sm text-slate-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
      </div>

      <button type="submit" class="w-full rounded-lg bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-400">
        Registrar
      </button>
    </form>
  </main>
</body>
</html>
