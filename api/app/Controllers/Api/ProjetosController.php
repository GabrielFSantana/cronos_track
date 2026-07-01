<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProjetoModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProjetosController extends BaseController
{
    public function index(): ResponseInterface
    {
        $model    = new ProjetoModel();
        $projetos = $model->orderBy('nome', 'ASC')->findAll();

        return $this->response->setStatusCode(200)->setJSON([
            'status'   => 'success',
            'projetos' => array_map([$this, 'castIds'], $projetos),
        ]);
    }

    /**
     * "cor" é opcional — se omitida, o banco aplica o DEFAULT '#888888'.
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Corpo da requisição inválido.',
            ]);
        }

        $row = ['nome' => $payload['nome'] ?? null];

        if (! empty($payload['cor'])) {
            $row['cor'] = $payload['cor'];
        }

        $model = new ProjetoModel();
        $id    = $model->insert($row, true);

        if ($id === false) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Dados inválidos.',
                'errors'  => $model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => 'success',
            'projeto' => $this->castIds($model->find($id)),
        ]);
    }

    private function castIds(array $projeto): array
    {
        $projeto['id'] = (int) $projeto['id'];

        return $projeto;
    }
}
