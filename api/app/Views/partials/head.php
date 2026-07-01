<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#080b12">
<title><?= esc($title ?? 'Cronos') ?></title>
<link rel="icon" type="image/png" href="<?= base_url('assets/cronos-logo.png') ?>">
<link rel="apple-touch-icon" href="<?= base_url('assets/cronos-logo.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
<script>
  tailwind = {
    config: {
      theme: {
        extend: {
          fontFamily: {
            sans: ['DM Sans', 'ui-sans-serif', 'system-ui'],
            display: ['Manrope', 'ui-sans-serif', 'system-ui'],
          },
          colors: {
            ink: '#080b12',
            lime: '#c6ff4a',
            violet: '#8b7cff',
            coral: '#ff7163',
          },
          boxShadow: {
            glow: '0 0 50px rgba(198, 255, 74, 0.12)',
            card: '0 24px 80px rgba(0, 0, 0, 0.22)',
          },
        },
      },
    },
  };
</script>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  :root { color-scheme: dark; }
  html { scroll-behavior: smooth; }
  body {
    background:
      radial-gradient(circle at 82% -10%, rgba(139, 124, 255, .14), transparent 32rem),
      radial-gradient(circle at -8% 42%, rgba(198, 255, 74, .08), transparent 28rem),
      #080b12;
  }
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    opacity: .17;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.12'/%3E%3C/svg%3E");
  }
  ::selection { background: rgba(198, 255, 74, .28); color: #fff; }
  .glass {
    background: linear-gradient(145deg, rgba(255,255,255,.068), rgba(255,255,255,.025));
    border: 1px solid rgba(255,255,255,.09);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.035);
    backdrop-filter: blur(18px);
  }
  .field {
    width: 100%;
    border: 1px solid rgba(255,255,255,.1);
    background: rgba(255,255,255,.045);
    color: #f8fafc;
    border-radius: 1rem;
    padding: .8rem 1rem;
    outline: none;
    transition: border-color .2s, background .2s, box-shadow .2s;
  }
  .field:hover { background: rgba(255,255,255,.065); }
  .field:focus {
    border-color: rgba(198,255,74,.62);
    background: rgba(255,255,255,.075);
    box-shadow: 0 0 0 4px rgba(198,255,74,.08);
  }
  .field::-webkit-calendar-picker-indicator { filter: invert(1); opacity: .55; cursor: pointer; }
  select.field { color-scheme: dark; }
  .eyebrow { letter-spacing: .16em; text-transform: uppercase; }
  .brand-mark {
    background-image: url("<?= base_url('assets/cronos-logo.png') ?>");
    background-repeat: no-repeat;
    background-position: center 21%;
    background-size: 190%;
  }
  @media (prefers-reduced-motion: no-preference) {
    .reveal { animation: reveal .55s ease-out both; }
    .reveal-delay-1 { animation-delay: .08s; }
    .reveal-delay-2 { animation-delay: .16s; }
    @keyframes reveal { from { opacity: 0; transform: translateY(12px); } }
  }
</style>
