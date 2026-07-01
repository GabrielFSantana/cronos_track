<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiKeyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = $request->getHeaderLine('X-API-Key');

        if ($key === '' || ! hash_equals((string) env('API_KEY'), $key)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'API key inválida ou ausente.',
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer após a resposta.
    }
}
