<?php 

   class simplexMethod
   {
      private $table = array(array());
      
      public $basis = array();
      private $m = 0;
      private $n = 0;

      public function __construct($source) {
         $this->m = count($source);
         $this->n = count($source[0]);
         for ($i=0; $i < $this->m; $i++) { 
            for ($j=0; $j < $this->n+$this->m-1; $j++) { 
               if ($j < $this->n)
               $this->table[$i][$j] = $source[$i][$j];
               else
               $this->table[$i][$j] = 0;
            }
            if (($this->n + $i) < $this->n+$this->m-1)
            {
               $this->table[$i][$this->n + $i] = 1;
               array_push($this->basis, $this->n + $i);
            }
         }
         $this->n = $this->n+$this->m-1;
      } 

      public function calculate($result) {
         $mainCol = 0;
         $mainRow = 0;
         
         while (!($this->IsItEnd())) {
            $mainCol = $this->findMainCol();
            $mainRow = $this->findMainRow($mainCol);
            $this->basis[$mainRow] = $mainCol;
            $new_table = array(array());
            for ($j = 0; $j <$this->n; $j++){
               $new_table[$mainRow][$j] = $this->table[$mainRow][$j] / $this->table[$mainRow][$mainCol];
            }
            for ($i = 0; $i < $this->m; $i++)
            {
               if ($i == $mainRow)
                  continue;

               for ($j = 0; $j < $this->n; $j++)
               {
                  $new_table[$i][$j] = $this->table[$i][$j] - $this->table[$i][$mainCol] * $new_table[$mainRow][$j];
               }
            }
            $this->table = $new_table;
         }
         
         for ($i = 0; $i < count($result); $i++)
         {
            $k=-1;
            if (!is_bool(array_search($i + 1, $this->basis)))
               $k=array_search($i + 1, $this->basis);
            if ($k!=-1)
                  $result[$i] = $this->table[$k][0];
            else 
               $result[$i]=0;
         }

         return $result;
      }

      public function IsItEnd() {
         $flag = true;

            for ($j = 1; $j < $this->n; $j++)
            {
                if ($this->table[$this->m - 1][$j] < 0)
                {
                    $flag = false;
                    break;
                }
            }
            return $flag;
      }

      private function findMainCol()
        {
            $mainCol2 = 1;
            
            for ($j = 2; $j < $this->n; $j++)
                if ($this->table[$this->m - 1][$j] < $this->table[$this->m - 1][$mainCol2])
                    $mainCol2 = $j;
            return $mainCol2;
        }

        private function findMainRow($mainCol1)
        {
            $mainRow1 = 0;
            
            for ($i = 0; $i < $this->m - 1; $i++)
                if ($this->table[$i][$mainCol1] > 0)
                {
                    $mainRow1 = $i;
                    break;
                }

            for ($i = $mainRow1 + 1; $i < $this->m - 1; $i++)
                if (($this->table[$i][$mainCol1] > 0) && (($this->table[$i][0] / $this->table[$i][$mainCol1]) < ($this->table[$mainRow1][0] / $this->table[$mainRow1][$mainCol1])))
                    $mainRow1 = $i;

            return $mainRow1;
        }
   }
?>