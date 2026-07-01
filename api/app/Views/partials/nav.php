<?php $active ??= ''; ?>
<header class="sticky top-0 z-50 border-b border-white/[.06] bg-ink/80 backdrop-blur-xl">
  <div class="mx-auto flex h-[76px] max-w-7xl items-center justify-between px-5 sm:px-8">
    <a href="<?= site_url('/') ?>" class="group flex items-center gap-3" aria-label="Cronos — início">
      <span class="brand-mark relative h-11 w-11 overflow-hidden rounded-2xl border border-white/10 bg-ink shadow-[0_0_24px_rgba(80,120,255,.18)] transition-transform duration-300 group-hover:-rotate-6" aria-hidden="true"></span>
      <span>
        <span class="block font-display text-[17px] font-extrabold leading-none tracking-[-.03em] text-white">CRONOS</span>
        <span class="eyebrow mt-1 block bg-gradient-to-r from-[#24bfff] to-[#8139ff] bg-clip-text text-[8px] font-bold text-transparent">Track</span>
      </span>
    </a>

    <nav class="flex items-center gap-1 rounded-2xl border border-white/[.07] bg-white/[.035] p-1.5 text-sm font-semibold" aria-label="Navegação principal">
      <a href="<?= site_url('/') ?>"
         class="flex items-center gap-2 rounded-xl px-3 py-2 transition sm:px-4 <?= $active === 'dashboard' ? 'bg-white/[.1] text-white shadow-sm' : 'text-slate-500 hover:bg-white/[.05] hover:text-slate-200' ?>">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 13h6V4H4v9Zm0 7h6v-3H4v3Zm10 0h6v-9h-6v9Zm0-13h6V4h-6v3Z"/>
        </svg>
        <span class="hidden sm:inline">Visão geral</span>
      </a>
      <a href="<?= site_url('lancamento') ?>"
         class="flex items-center gap-2 rounded-xl px-3 py-2 transition sm:px-4 <?= $active === 'lancamento' ? 'bg-lime text-ink shadow-glow' : 'text-slate-500 hover:bg-white/[.05] hover:text-slate-200' ?>">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/>
        </svg>
        <span class="hidden sm:inline">Novo registro</span>
      </a>
      <a href="<?= site_url('projetos') ?>"
         class="flex items-center gap-2 rounded-xl px-3 py-2 transition sm:px-4 <?= $active === 'projetos' ? 'bg-white/[.1] text-white shadow-sm' : 'text-slate-500 hover:bg-white/[.05] hover:text-slate-200' ?>">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>
        </svg>
        <span class="hidden sm:inline">Projetos</span>
      </a>
    </nav>
  </div>
</header>
