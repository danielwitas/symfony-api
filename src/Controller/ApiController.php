<?php


namespace App\Controller;


use App\Api\ApiProblem;
use App\Exception\ApiProblemException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    protected $entityManager;
    protected $userPasswordEncoder;
    protected $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        SerializerInterface $serializer
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->serializer = $serializer;
    }

    protected function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);
        $apiProblem = new ApiProblem(
            Response::HTTP_BAD_REQUEST,
            ApiProblem::TYPE_VALIDATION_ERROR
        );
        $apiProblem->set('errors', $errors);
        throw new ApiProblemException($apiProblem);
    }

    protected function isJsonValid($data)
    {
        if (null === $data) {
            $apiProblem = new ApiProblem(
                Response::HTTP_BAD_REQUEST,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );
            throw new ApiProblemException($apiProblem);
        }
    }

    protected function isFormValid(FormInterface $form)
    {
        if (false === $form->isValid()) {
            return $this->throwApiProblemValidationException($form);
        }
    }

    protected function jsonDecode($data)
    {
        return json_decode($data, true);
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
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
}