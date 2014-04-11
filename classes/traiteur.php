<?php

abstract class Traiteur
{
	protected $csvLoader;
	protected $path;

	abstract public function traiter($filename);
	abstract public function suppression($filename);
	abstract public function maj();
}