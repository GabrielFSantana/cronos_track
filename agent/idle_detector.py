"""Detecta há quanto tempo não há input de mouse/teclado no Windows.

Usa GetLastInputInfo da Win32 API via ctypes (não depende do pywin32).
"""

import ctypes


class _LASTINPUTINFO(ctypes.Structure):
    _fields_ = [("cbSize", ctypes.c_uint), ("dwTime", ctypes.c_uint)]


def get_idle_seconds() -> float:
    info = _LASTINPUTINFO()
    info.cbSize = ctypes.sizeof(_LASTINPUTINFO)
    ctypes.windll.user32.GetLastInputInfo(ctypes.byref(info))

    millis_since_input = ctypes.windll.kernel32.GetTickCount() - info.dwTime
    return millis_since_input / 1000.0
