<?php
namespace Oculie\Core\Inquirer;

class Repository
{
    protected $subject;

    public function __construct($subject)
    {
        $this->subject = $subject;
        \Oculie\Debug::dump([$this->subject], false);
    }

    public function getRecordFields()
    {
        return $this->subject->getFields();
    }
}