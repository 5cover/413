<?php

namespace ValueObjects;
enum LibelleAbonnement: string
{
    case Gratuit = 'gratuit';
    case Standard = 'standard';
    case Premium = 'premium';
}