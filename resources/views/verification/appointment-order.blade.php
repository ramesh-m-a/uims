<h2>Appointment Order Verification</h2>

<p>Order No: {{ $order->order_number }}</p>
<p>Status: VALID</p>
<p>Examiner: {{ $order->examiner->name }}</p>
<p>Generated At: {{ $order->generated_at }}</p>
