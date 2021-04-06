<?php
namespace Oculie\Core\Handler;

class Database extends \Oculie\Core\Definition\Subroutine\Handler\Database
{
    /*
     * Behavior
     */

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function handle($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function createRepository($repoName)
    {
        $this->repository[$repoName] = new \Oculie\Core\Builder\Repository();
        return $this->repository[$repoName];
    }

    public function setRepository($name, $dataDefinition){}

    public function getRepository($name){}

    public function getRepositories()
    {
        return $this->repository;
    }

    /*
     * routines & properties
     */

    protected $name;
    protected $subject;
    protected $repository    = [];
}