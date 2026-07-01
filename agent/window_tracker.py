"""Captura o app e o título da janela ativa (em foco) no Windows."""

from dataclasses import dataclass

import psutil
import win32gui
import win32process


@dataclass
class ActiveWindow:
    app: str
    titulo: str


def get_active_window() -> ActiveWindow | None:
    """Retorna a janela em foco, ou None se não for possível identificá-la
    (ex.: tela de bloqueio, desktop seguro, processo já encerrado)."""
    hwnd = win32gui.GetForegroundWindow()
    if not hwnd:
        return None

    titulo = win32gui.GetWindowText(hwnd)

    try:
        _, pid = win32process.GetWindowThreadProcessId(hwnd)
        app = psutil.Process(pid).name()
    except (psutil.NoSuchProcess, psutil.AccessDenied):
        return None

    return ActiveWindow(app=app, titulo=titulo)
