<?php

namespace App\Repositories\CreditCards;

use App\Models\CreditCard;
use App\Repositories\BaseRepository;

class CreditCardRepository extends BaseRepository implements CreditCardRepositoryInterface
{
	public function getModel(): string
	{
		return CreditCard::class;
	}
}


