<?php

namespace App\ParamConverter;

use App\Model\JiraProject;
use App\Service\CacheLoader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ProjectParamConverter implements ParamConverterInterface
{
    protected CacheLoader $cacheLoader;

    public function __construct(CacheLoader $cacheLoader)
    {
        $this->cacheLoader = $cacheLoader;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $found = array_filter($this->cacheLoader->getProjectList(), function (JiraProject $project) use ($request, $configuration) {
            return $project->getId() === $request->attributes->get($configuration->getName());
        });

        $resolvedProject = current($found);
        if (empty($resolvedProject) || empty($resolvedProject->getName())) {
            return false;
        }
        $request->attributes->set('resolvedProject', $resolvedProject);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return
            JiraProject::class === $configuration->getClass()
            && 'projectId' === $configuration->getName();
    }

}
