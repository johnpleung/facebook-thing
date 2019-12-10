<?php
	class data_definition {
		public $col_name;
		public $col_declaration;
		public $path;
		public $sprintf_type;

		public function __construct($properties){
			if(isset($properties['col_declaration'])){
				$this->col_declaration = $properties['col_declaration'];
			}
			if(isset($properties['col_name'])){
				$this->col_name = $properties['col_name'];
			}

			if(isset($properties['sprintf_type'])){
				$this->sprintf_type = $properties['sprintf_type'];
			}

			if(isset($properties['path'])){
				$this->path = $properties['path'];
			}
		}
	}