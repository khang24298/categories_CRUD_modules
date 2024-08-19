<?php

namespace App\Enums;

enum CategoryType: string
{
   case SERVICE = 'service';
   case PRODUCT = 'product';
   case TERMS = 'terms';
}