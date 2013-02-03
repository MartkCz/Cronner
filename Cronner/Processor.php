<?php

namespace stekycz\Cronner;

use Nette\Object;
use ReflectionMethod;

/**
 * @author Martin Štekl <martin.stekl@gmail.com>
 * @since 2013-02-03
 */
final class Processor extends Object {

	/**
	 * @var \stekycz\Cronner\Tasks[]
	 */
	protected $tasks = array();

	/**
	 * Adds task case to be processed when cronner runs. If tasks
	 * with name which is already added are given then throws
	 * an exception.
	 *
	 * @param \stekycz\Cronner\Tasks $tasks
	 * @return \stekycz\Cronner\Cronner
	 * @throws \stekycz\Cronner\InvalidArgumentException
	 */
	public function addTaskCase(Tasks $tasks) {
		if (array_key_exists($tasks->getName(), $this->tasks)) {
			throw new InvalidArgumentException("Tasks with name '" . $tasks->getName() . "' have been already added.");
		}
		$this->tasks[$tasks->getName()] = $tasks;

		return $this;
	}

	/**
	 * Runs all registered tasks.
	 */
	public function process() {
		foreach ($this->tasks as $task) {
			$this->process($task);
		}
	}

	/**
	 * Processes all tasks in given object.
	 *
	 * @param \stekycz\Cronner\Tasks $tasks
	 */
	private function processTasks(Tasks $tasks) {
		$reflection = $tasks->getReflection();
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

		foreach ($methods as $method) {
			if (Parameters::shouldBeRun($method)) {
				$tasks->{$method->getName()}();
			}
		}
	}

}
