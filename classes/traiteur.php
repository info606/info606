<?php

abstract class Traiteur
{
	protected $csvLoader;
	protected $path;

	abstract public function ajout(String $filename);
	abstract public function suppression(String $filename);
	abstract public function maj();
}