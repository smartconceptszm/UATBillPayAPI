<?php
namespace App\Http\Services\Enums;

use Ramsey\Uuid\Type\Integer;

class ChartColours 
{

	public static function getColours($key)
	{

		$chartColours = [ 
								1 => [
												'backgroundColor'=> "#02AC56",//"rgba(75,192,192,1)",
												'borderColor' => "#02AC56",
												'pointBackgroundColor' =>"#02AC56",
												'pointBorderColor' => "#02AC56",
											],
								2 => [
											'backgroundColor'=> '#F7C405',"rgba(255, 255, 255,0.2)",
											'borderColor' => '#F7C405',
											'pointBackgroundColor' => '#F7C405',
											'pointBorderColor' => '#F7C405',
										],
								3 => [
											'backgroundColor' => '#1486E4',//'rgba(130, 221, 44, 0.2)',
											'borderColor' => '#1486E4',
											'pointBackgroundColor' => '#1486E4',
											'pointBorderColor' => '#fff',
										],
								4 => [
											'backgroundColor'=> "#E61C23",//"rgba(75,192,192,1)",
											'borderColor' => "#E61C23",
											'pointBackgroundColor' =>"#E61C23",
											'pointBorderColor' => "#E61C23",
										],
								5 => [
											'backgroundColor'=> '#581845',//"rgba(51,153,254,1)",
											'borderColor' => '#581845',
											'pointBackgroundColor' => '#581845',
											'pointBorderColor' => '#581845',
										],
								6 => [
											'backgroundColor'=> "#02AC56",//"rgba(75,192,192,1)",
											'borderColor' => "#02AC56",
											'pointBackgroundColor' =>"#02AC56",
											'pointBorderColor' => "#02AC56",
										],
								7 => [
											'backgroundColor'=> '#F7C405',"rgba(255, 255, 255,0.2)",
											'borderColor' => '#F7C405',
											'pointBackgroundColor' => '#F7C405',
											'pointBorderColor' => '#F7C405',
										],
								8 => [
											'backgroundColor' => '#1486E4',//'rgba(130, 221, 44, 0.2)',
											'borderColor' => '#1486E4',
											'pointBackgroundColor' => '#1486E4',
											'pointBorderColor' => '#fff',
										],
								9 => [
											'backgroundColor'=> "#E61C23",//"rgba(75,192,192,1)",
											'borderColor' => "#E61C23",
											'pointBackgroundColor' =>"#E61C23",
											'pointBorderColor' => "#E61C23",
										],
								10 => [
											'backgroundColor'=> '#581845',//"rgba(51,153,254,1)",
											'borderColor' => '#581845',
											'pointBackgroundColor' => '#581845',
											'pointBorderColor' => '#581845',
										],
										

							];
		return $chartColours[$key];
		
	}

}
