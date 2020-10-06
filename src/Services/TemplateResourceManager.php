<?php


namespace App\Services;


use App\Entity\Template;
use App\Form\TemplateType;
use Symfony\Component\HttpFoundation\Request;

class TemplateResourceManager extends ApiResourceManager
{
    const TEMPLATE_DELETED_MESSAGE = 'Template has been deleted';
    const TEMPLATE_UPDATED_MESSAGE = 'Template has been updated';
    const TEMPLATE_ADDED_MESSAGE = 'Template has been added';

    public function getSingleTemplate($id) :Template
    {
        return $this->findTemplateById($id);
    }

    public function getTemplateCollection(Request $request)
    {
        $filter = $request->query->get('filter');
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You have to be logged in to browse templates');
        $user = $this->token->getToken()->getUser();
        $qb = $this->entityManager->getRepository(Template::class)->findAllQueryBuilder($user, $filter);
        $paginatedCollection = $this->paginationFactory->createCollection($qb, $request, 'templates_get_collection');
        return $paginatedCollection->getResult('templates');
    }

    public function deleteTemplate(int $id)
    {
        $template = $this->findTemplateById($id);
        $this->entityManager->remove($template);
        $this->entityManager->flush();
    }

    public function addTemplate(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'You have to be logged in to add templates');
        $form = $this->createForm(TemplateType::class, new Template());
        $template = $this->apiValidator->processForm($request, $form);
        $user = $this->token->getToken()->getUser();
        $template->setOwner($user);
        $this->entityManager->persist($template);
        $this->entityManager->flush();
    }

    public function updateTemplate(Request $request, int $id)
    {
        $existingTemplate = $this->findTemplateById($id);
        $form = $this->createForm(TemplateType::class, $existingTemplate);
        $this->apiValidator->processForm($request, $form);
        $this->entityManager->flush();
    }

    private function findTemplateById(int $id): Template
    {
        /** @var Template $template */
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(['id' => $id]);
        if (!$template) {
            throw $this->createNotFoundException('Template does not exist');
        }
        $this->denyAccessUnlessGranted('OWNER', $template, 'This template does not belong to you');
        return $template;
    }



}