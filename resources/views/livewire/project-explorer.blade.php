<div >
	<form wire:submit.prevent="analyze" >
		<input type="file" wire:model="class_file" >

		@error('class_file') <span class="error" >{{ $message }}</span > @enderror

		<button type="submit" >Analyze file</button >
	</form >

	<table style="width: 100%; min-height: 100vh">
		<thead >
		<tr >
			<th >New version</th >
			<th >Original</th >
		</tr >
		</thead >
		<tbody >
		<tr >
			<td style="width: 50%">
				<pre wire:loading.remove >
					{{  $commented_class  }}
				</pre >
			</td >
			<td style="width: 50%" >
				<pre wire:loading.remove >
					{{ $contents }}
				</pre >
			</td >
		</tr >
		</tbody >
	</table >
</div >
