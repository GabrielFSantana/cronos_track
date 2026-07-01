<?php

namespace App\Controllers;

use App\Models\ProjetoModel;
use CodeIgniter\HTTP\RedirectResponse;

class ProjetosPageController extends BaseController
{
    public function index(): string
    {
        return view('projetos', [
            'projetos' => (new ProjetoModel())->orderBy('nome', 'ASC')->findAll(),
            'errors'   => session()->getFlashdata('errors') ?? [],
            'success'  => session()->getFlashdata('success'),
        ]);
    }

    public function store(): RedirectResponse
    {
        $row = ['nome' => $this->request->getPost('nome')];

        if ($cor = $this->request->getPost('cor')) {
            $row['cor'] = $cor;
        }

        $model = new ProjetoModel();

        if (! $model->insert($row)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/projetos')->with('success', 'Projeto criado com sucesso.');
    }
}
