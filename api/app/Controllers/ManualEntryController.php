<?php

namespace App\Controllers;

use App\Models\ActivityModel;
use App\Models\ProjetoModel;
use CodeIgniter\HTTP\RedirectResponse;

class ManualEntryController extends BaseController
{
    public function create(): string
    {
        return view('manual_entry', [
            'projetos' => (new ProjetoModel())->orderBy('nome', 'ASC')->findAll(),
            'errors'   => session()->getFlashdata('errors') ?? [],
            'success'  => session()->getFlashdata('success'),
        ]);
    }

    public function store(): RedirectResponse
    {
        $row = [
            'origem'         => 'manual',
            'app_ou_dominio' => $this->request->getPost('app_ou_dominio'),
            'titulo'         => $this->request->getPost('titulo') ?: null,
            'projeto_id'     => $this->request->getPost('projeto_id') ?: null,
            'inicio'         => $this->normalizeDateTime($this->request->getPost('inicio')),
            'fim'            => $this->normalizeDateTime($this->request->getPost('fim')),
        ];

        if (! empty($row['inicio']) && ! empty($row['fim']) && strtotime($row['fim']) <= strtotime($row['inicio'])) {
            return redirect()->back()->withInput()->with('errors', [
                'fim' => '"Fim" deve ser posterior a "Início".',
            ]);
        }

        $model = new ActivityModel();

        if (! $model->insert($row)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/lancamento')->with('success', 'Lançamento registrado com sucesso.');
    }

    /**
     * O input datetime-local do navegador manda "2026-07-01T20:00";
     * o banco espera "2026-07-01 20:00:00".
     */
    private function normalizeDateTime(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : $value;
    }
}
