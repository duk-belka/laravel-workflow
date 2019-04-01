<?php
/**
 * Created by PhpStorm.
 * User: neduck
 * Date: 2019-04-01
 * Time: 16:45
 */

declare(strict_types = 1);

namespace Brexis\LaravelWorkflow;

use Symfony\Component\Workflow\Workflow;

interface WorkflowLibrarianInterface
{
    /**
     * Return the $subject workflow
     *
     * @param  object      $subject
     * @param  string|null $workflowName
     *
     * @return Workflow
     */
    public function get($subject, ?string $workflowName = null): Workflow;


    /**
     * Add a workflow to the subject
     *
     * @param Workflow $workflow
     * @param string   $supportStrategy
     */
    public function add(Workflow $workflow, string $supportStrategy): void;

    /**
     * Add a workflow to the registry from array
     *
     * @param  string $name
     * @param  array  $workflowData
     *
     * @throws \ReflectionException
     */
    public function addFromArray(string $name, array $workflowData): void;
}