<?php


namespace App\Validation;


use App\Api\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiValidator
{

    public function processForm(Request $request, FormInterface $form)
    {
        $data = $this->jsonDecode($request);
        $this->isJsonValid($data);
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
        $this->isFormValid($form);
        return $form->getData();
    }

    protected function jsonDecode(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    protected function isJsonValid(array $data)
    {
        if (null === $data) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );
            throw new ApiProblemException($apiProblem);
        }
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }

    protected function isFormValid(FormInterface $form)
    {
        if (false === $form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_VALIDATION_ERROR
            );
            $apiProblem->set('errors', $errors);
            throw new ApiProblemException($apiProblem);
        }
    }
}