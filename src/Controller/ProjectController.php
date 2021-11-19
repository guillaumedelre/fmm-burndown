<?php

namespace App\Controller;

use App\Model\JiraCurrentSprint;
use App\Service\Burndown;
use App\Service\CacheLoader;
use App\Service\JiraClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use App\Model\JiraSprint;
use App\Model\JiraProject;

/**
 * @Route("/", name="project_")
 */
class ProjectController extends AbstractController
{
    protected JiraClient $jiraClient;
    protected CacheLoader $jiraCache;
    protected RouterInterface $router;
    protected Burndown $burndown;

    public function __construct(JiraClient $jiraClient, RouterInterface $router, CacheLoader $jiraCache, Burndown $burndown)
    {
        $this->jiraClient = $jiraClient;
        $this->router = $router;
        $this->jiraCache = $jiraCache;
        $this->burndown = $burndown;
    }

    /**
     * @Route("/{projectId}", name="burndown_current_sprint")
     * @ParamConverter("projectId", class=JiraProject::class, isOptional=false)
     */
    public function burndownForCurrentSprint(JiraProject $resolvedProject): Response
    {
        if (empty($resolvedSprint)) {
            $resolvedSprint = $this->jiraCache->getCurrentSprint($resolvedProject->getId());
        }

        return $this->redirectToRoute(
            'project_burndown_for_sprint',
            [
                'projectId' => $resolvedProject->getId(),
                'sprintId'  => $resolvedSprint->getId(),
            ]
        );
    }

    /**
     * @Route("/{projectId}/{sprintId}", name="burndown_for_sprint")
     * @ParamConverter("projectId", class=JiraProject::class, isOptional=false)
     * @ParamConverter("sprintId", class=JiraSprint::class, isOptional=false)
     */
    public function burndownForSprint(JiraProject $resolvedProject, JiraSprint $resolvedSprint): Response
    {
        if (empty($resolvedSprint)) {
            $resolvedSprint = $this->jiraCache->getCurrentSprint($resolvedProject->getId());
        }

        return $this->render(
            'burndown/index.html.twig',
            [
                'projectsListUrl' => $this->router->generate(
                    'ajax_projects_list',
                    [
                        'projectId' => $resolvedProject->getId(),
                    ]
                ),
                'sprintstListUrl' => $this->router->generate(
                    'ajax_sprints_list',
                    [
                        'projectId' => $resolvedProject->getId(),
                        'sprintId'  => $resolvedSprint->getId(),
                    ]
                ),
                'project'         => $resolvedProject,
                'sprint'          => $resolvedSprint,
                'burndown'        => $this->burndown->compute($resolvedProject, $resolvedSprint),
            ]
        );
    }

    /**
     * @Route("/", name="index")
     */
    public function index(CacheLoader $jiraCache): Response
    {
        return $this->render(
            'default/index.html.twig',
            [
                'projects' => $jiraCache->getProjectList(),
            ]
        );
    }

}
