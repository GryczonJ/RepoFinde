<?php

namespace App\Controller;

class FindRepository
{
    public function getListRepositoryName(int $HowMeny = 50, DateTime $DateCreate, string $ProgramingLangage, bool $SortByAZ ): []
    {
        return new ArrayObject([
            'repository1',
            'repository2',
            'repository3'
        ]);
    }
   
}