<?php
session_start();
require_once('fns.php');

if     ($_REQUEST['op'] == 'travels_report') getTravelReport();
//elseif ($_REQUEST['op'] == 'daily-summary')  getDailyReportSummary();


function getTravelReport() {
	global $DB_CONNECTION;
	$date = mysqli_real_escape_string($DB_CONNECTION, $_POST['date']);
	
	$sql = "SELECT 		sb.travel_date, booked_seats, sb.fare, sb.booked_id, bus_no, seating_arrangement, route, sb.route_code, drivers_expenses, offline_charge, online_charge
			FROM   		seat_booking AS sb
			LEFT JOIN   manifest_audit AS ma ON ma.booked_id = sb.booked_id
			JOIN   		routes AS r ON sb.route_code = r.route_code
			WHERE		seat_status = 'Full' AND sb.travel_date = '$date' AND sb.travel_id = '" . TRAVEL_ID . "'
			ORDER BY 	sb.time_stamp";
	
	$result = $DB_CONNECTION->query($sql);
	
	$html = '';
	if ($result->num_rows > 0) {
		$n = 1; $total_expenses = 0; $total_service_charge = 0; $total_balance = 0; $total_sub_total = 0;
		while ($row = $result->fetch_object()) {
			$service_charge = 0; $sub_total = 0; $offline = 0; $online = 0;
			/*** Determine bus type and set appropriate fields ***/
			if ($row->seating_arrangement == 11) {
				$fare_field = 'executive_fare';
			} elseif ($row->seating_arrangement < 20) {
				$fare_field = 'hiace_fare';
			} else {
				$fare_field = 'luxury_fare';
			}
			
			$fare = getFare(TRAVEL_ID, $row->route_code, $fare_field);
			
			$fare_result = $DB_CONNECTION->query("SELECT fare, online FROM booking_details WHERE booked_id = '$row->booked_id'");
			while ($details = $fare_result->fetch_object()) {
				$sub_total += $details->fare;
				if ($details->online == "Yes") {
					$service_charge += $details->fare * ($row->online_charge / 100);
					$online++;
				} else {
					$service_charge += $details->fare * ($row->offline_charge / 100);
					$offline++;
				}
			}
			
			$num_of_tickets = count(explode(',', $row->booked_seats));
			$balance        = $sub_total - $row->drivers_expenses - $service_charge;
			
			$html .= "<tr><td>$n</td>
					  <td>{$row->bus_no}</td>
					  <td>{$row->route}</td>
					  <td>{$row->seating_arrangement} Seaters</td>
					  <td style='text-align:center'>{$num_of_tickets}</td>
					  <td>$offline</td>
					  <td>$online</td>
					  <td>₦" . number_format($fare) . "</td>
					  <td>₦" . number_format($sub_total) . "</td>
					  <td style='text-align:center'>₦" . number_format($row->drivers_expenses) . "</td>
					  <td style='text-align:center'>₦" . number_format($service_charge) . "</td>
					  <td>₦" . number_format($balance) . "</td>
				</tr>";
			++$n;
			$total_sub_total      += $sub_total;
			$total_expenses       += $row->drivers_expenses;
			$total_service_charge += $service_charge;
			$total_balance        += $balance;
		}
		$html .= "<tr><th colspan='8'>TOTAL</th>
				  <td style='text-align:right'><b>₦" . number_format($total_sub_total) . "</b></td>
				  <td style='text-align:right'><b>₦" . number_format($total_expenses) . "</b></td>
				  <td style='text-align:right'><b>₦" . number_format($total_service_charge) . "</b></td>
				  <td style='text-align:right'><b>₦" . number_format($total_balance) . "</b></td></tr>"; 
	}
	echo $html;
}


function getFare($travel_id, $route_code, $fare_field) {
	global $DB_CONNECTION;
	
	$result = $DB_CONNECTION->query("SELECT {$fare_field} FROM fares WHERE travel_id = '$travel_id' AND route_code = '$route_code'");
	return $result->fetch_object()->$fare_field;
}



#function getDailyReportSummary() {
#	global $DB_CONNECTION;
#	
#	$date = mysqli_real_escape_string($DB_CONNECTION, $_POST['day']);
#	$sql = "SELECT    COUNT(*) AS no_of_buses, fare, route, sb.route_code, seating_arrangement, booked_seats, SUM(drivers_expenses) AS total_expenses, load_cost, insurance, fare 
#			FROM      seat_booking AS sb
#			LEFT JOIN manifest_audit AS ma ON sb.booked_id = ma.booked_id
#			JOIN   	  routes AS r ON sb.route_code = r.route_code
#			WHERE     booked_seats <> '' AND sb.travel_date = '$date'
#			GROUP BY  seating_arrangement, sb.route_code
#			ORDER BY  route";
#	$result = $DB_CONNECTION->query($sql);
#	
#	if ($result->num_rows > 0) {
#		$summary = "<b>Daily summary</b> [ " .  date('D d M Y', strtotime($date)) . " ]<hr style='margin:4px 0px' /><br />";
#		$total_expenses = 0; $total_income = 0;
#		while ($row = $result->fetch_object()) {
#			$total_expenses += $row->total_expenses;
#			$num_of_tickets  = count(explode(',', $row->booked_seats));
#			$income          = $num_of_tickets * $row->fare;
#			$total_income   += $income - $row->total_expenses - $row->load_cost - $row->insurance;
#			$summary .= "<b>{$row->route} [ ₦" . number_format($row->fare) . " ]</b><br />
#						 &nbsp; &nbsp; {$row->seating_arrangement} seaters [ {$row->no_of_buses} ]<br />
#						 &nbsp; &nbsp; Number of tickets sold: {$num_of_tickets}<br />
#						 &nbsp; &nbsp; Initial income: ₦" . number_format($income) . "<br />
#						 &nbsp; &nbsp; Total expenses: ₦" . number_format($row->total_expenses) . "<br />
#						 &nbsp; &nbsp; Balance: ₦" . number_format($income - $row->total_expenses - $row->load_cost - $row->insurance) . "<hr style='margin:5px 0px' />";
#		}
#		$summary .= "&nbsp; &nbsp; <b>Total expenses:</b> ₦ " . number_format($total_expenses) . "<br />
#					 &nbsp; &nbsp; <b>Total balance :</b> ₦ " . number_format($total_income) . "<br />
#					 &nbsp; &nbsp; <b>Service charge:</b> ₦" . number_format(($_SESSION['oya_percentage'] / 100) * $total_income);
#	} else {
#		echo "No record was found for this date";
#		return;
#	}
#	echo $summary;
#}
?>