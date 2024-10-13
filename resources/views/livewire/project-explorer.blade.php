<div >
    <form wire:submit.prevent="analyze" >
        <input type="file" wire:model="class_file" >

        @error('class_file') <span class="error" >{{ $message }}</span > @enderror

        <button type="submit" >Analyze file</button >
    </form >

    <pre >
        {!!  $commented_class  !!}
    </pre >
</div >
