<?php

abstract class Traiteur
{
	protected $csvLoader;
	protected $path;

	abstract public function ajout($filename);
	abstract public function suppression($filename);
	abstract public function maj();
}