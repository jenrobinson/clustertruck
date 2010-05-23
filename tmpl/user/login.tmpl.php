<form id="member-form" class="<?php if (!isset($error)){ echo 'hidden'; } ?>" method="post" action="<?php echo $this->url('login'); ?>">
		<input type="hidden" name="submit" value="true">
		<input type="hidden" name="return" value="">
		<input type="hidden" name="flow" value="<?php echo $flow; ?>">
			<ul class="form">
				<li>
					<label>
						<em>Email Address</em>
						<input type="text" name="email" value="<?php echo p('email'); ?>">
					</label>
				</li>
				<li>
					<label>
						<em>Password</em>
						<input type="password" name="pass">
					</label>
				</li>
				<li><button type="submit">Login</button></li>
			</ul>			
		</form>	