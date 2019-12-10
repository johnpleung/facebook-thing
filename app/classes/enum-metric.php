<?php
	class enum_metric extends metric_base {
		private $enums_definition;
		private $data;
		private $required_columns;

		public function __construct($properties = null){

			if(isset($properties['name'])){
				$this->name = $properties['name'];
			}

			if(isset($properties['path'])){
				$this->path = $properties['path'];
			}

			if(isset($properties['enums_definition'])){
				$this->enums_definition = $properties['enums_definition'];
			}

		}

		public function get_required_columns(){
			return $this->required_columns;
		}

		public function get_column_values($data_raw, $index = null){

			// Prepare an array or array of arrays that represents data to be included in a database row to be written. Return value includes column name, column value, and the sprintf type of the value, used for the SQL statement

			try {

				// Parse data passed in
				if(!$this->parse_data($data_raw)){
					throw new Exception('Could not parse data for enum metric ' . $this->name);
				}

				// Parse value from data
				$path = $this->path;
				if(isset($index)){
					$path = str_replace('{{INDEX}}', $index, $path);
				}
				$enums = $this->data_parser->{$path}[0];
				if(!$enums){
					return false;
					//throw new Exception('Could not get value for enum metric ' . $this->name);
				}

				$ret = [];
				$required_columns = [];
				foreach($enums as $name => $value){
					$col_name = substr($this->name . '_' . str_replace(' ','',$name), 0, 64); // Make sure dynamic columm name doesn't exceed 64 characters
					$ret[] = [
						'name' => $col_name,
						'value' => $value,
						'sprintf_type' => $this->enums_definition->sprintf_type
					];
					$required_columns[$col_name] = [
						'name' => $col_name,
						'declaration' => $this->enums_definition->col_declaration
					];
				}
				$this->required_columns = $required_columns;
				$this->data = $ret;
				return $ret;
			}catch(Exception $err){
				_log('error', [
					'message' => $err->getMessage(),
					'notes' => $data_raw
				]);
			}
			return false;
		}

	}