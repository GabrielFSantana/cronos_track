<?php $active ??= ''; ?>
<header class="sticky top-0 z-10 border-b border-slate-800 bg-slate-950/80 backdrop-blur">
  <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
    <a href="<?= site_url('/') ?>" class="flex items-center gap-2 text-lg font-semibold text-slate-100">
      <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500 text-base">⏱️</span>
      TimeTracker
    </a>
    <nav class="flex gap-1 rounded-lg bg-slate-900 p-1 text-sm font-medium">
      <a href="<?= site_url('/') ?>"
         class="rounded-md px-3 py-1.5 transition <?= $active === 'dashboard' ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:text-slate-100' ?>">
        Dashboard
      </a>
      <a href="<?= site_url('lancamento') ?>"
         class="rounded-md px-3 py-1.5 transition <?= $active === 'lancamento' ? 'bg-indigo-500 text-white' : 'text-slate-400 hover:text-slate-100' ?>">
        Lançamento manual
      </a>
    </nav>
  </div>
</header>
