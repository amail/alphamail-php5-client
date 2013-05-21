<?php

    /*
    The MIT License

    Copyright (c) Robin Orheden, 2013 <http://amail.io/>
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
    */

	class Project
	{
		public $id, $name, $updated;

		public function __construct($id, $name, $updated)
		{
			$this->id = $id;
			$this->name = $name;
			$this->updated = $updated;
		}
	}
	
	class DetailedProject
	{
		public $id, $name, $subject, $signature_id, $template_id, $updated;

		public function __construct($id, $name, $subject, $signature_id, $template_id, $updated)
		{
			$this->id = $id;
			$this->name = $name;
			$this->subject = $subject;
			$this->signature_id = $signature_id;
			$this->template_id = $template_id;
			$this->updated = $updated;
		}
	}
	
?>