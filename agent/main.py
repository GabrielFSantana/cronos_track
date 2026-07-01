"""Agente Windows do TimeTracker.

Monitora o app em foco e envia sessões (início/fim) para a API quando o
app muda ou quando o usuário fica ocioso. Nunca envia uma linha por
polling — só quando uma sessão termina.
"""

import os
import threading
import time
from datetime import datetime, timedelta

import pystray
import requests
from dotenv import load_dotenv
from PIL import Image, ImageDraw

from idle_detector import get_idle_seconds
from window_tracker import get_active_window

load_dotenv()

API_BASE_URL = os.environ.get("API_BASE_URL", "http://127.0.0.1:8080")
API_KEY = os.environ["API_KEY"]
POLL_INTERVAL_SECONDS = float(os.environ.get("POLL_INTERVAL_SECONDS", 2))
IDLE_THRESHOLD_SECONDS = float(os.environ.get("IDLE_THRESHOLD_SECONDS", 300))
FLUSH_INTERVAL_SECONDS = float(os.environ.get("FLUSH_INTERVAL_SECONDS", 30))

DATETIME_FORMAT = "%Y-%m-%d %H:%M:%S"


class Tracker:
    """Roda inteiramente em uma única thread de fundo — sem necessidade
    de locks entre _tick/_flush. `stop()` é chamado de outra thread (menu
    da bandeja), mas threading.Event já é thread-safe."""

    def __init__(self) -> None:
        self._current: dict | None = None
        self._pending: list[dict] = []
        self._stop = threading.Event()

    def run(self) -> None:
        last_flush = time.monotonic()

        while not self._stop.is_set():
            self._tick()

            if time.monotonic() - last_flush >= FLUSH_INTERVAL_SECONDS:
                self._flush()
                last_flush = time.monotonic()

            self._stop.wait(POLL_INTERVAL_SECONDS)

        self._shutdown()

    def stop(self) -> None:
        self._stop.set()

    def _tick(self) -> None:
        idle_seconds = get_idle_seconds()

        if idle_seconds >= IDLE_THRESHOLD_SECONDS:
            self._close_current(datetime.now() - timedelta(seconds=idle_seconds))
            return

        window = get_active_window()
        if window is None:
            return

        if self._current is None:
            self._start(window)
        elif window.app != self._current["app_ou_dominio"]:
            self._close_current(datetime.now())
            self._start(window)
        else:
            self._current["titulo"] = window.titulo

    def _start(self, window) -> None:
        self._current = {
            "origem": "app",
            "app_ou_dominio": window.app,
            "titulo": window.titulo,
            "inicio": datetime.now().strftime(DATETIME_FORMAT),
        }

    def _close_current(self, fim: datetime) -> None:
        if self._current is None:
            return

        inicio = datetime.strptime(self._current["inicio"], DATETIME_FORMAT)
        if fim <= inicio:
            self._current = None
            return

        self._pending.append({**self._current, "fim": fim.strftime(DATETIME_FORMAT)})
        self._current = None

    def _flush(self) -> None:
        if not self._pending:
            return

        batch, self._pending = self._pending, []

        try:
            response = requests.post(
                f"{API_BASE_URL}/api/activities",
                json={"activities": batch},
                headers={"X-API-Key": API_KEY},
                timeout=10,
            )
            response.raise_for_status()
            print(f"[TimeTracker] Enviadas {len(batch)} sessão(ões).")
        except requests.RequestException as exc:
            print(f"[TimeTracker] Falha ao enviar, tentando de novo depois: {exc}")
            self._pending = batch + self._pending

    def _shutdown(self) -> None:
        self._close_current(datetime.now())
        self._flush()


def _build_icon_image() -> Image.Image:
    image = Image.new("RGBA", (64, 64), (0, 0, 0, 0))
    draw = ImageDraw.Draw(image)
    draw.ellipse((8, 8, 56, 56), fill="#3366ff")
    return image


def main() -> None:
    tracker = Tracker()

    def on_quit(icon: pystray.Icon, _item) -> None:
        tracker.stop()
        icon.stop()

    icon = pystray.Icon(
        "timetracker",
        _build_icon_image(),
        "TimeTracker Agent",
        menu=pystray.Menu(pystray.MenuItem("Sair", on_quit)),
    )

    def setup(icon: pystray.Icon) -> None:
        icon.visible = True
        tracker.run()

    try:
        icon.run(setup=setup)
    except KeyboardInterrupt:
        tracker.stop()


if __name__ == "__main__":
    main()
