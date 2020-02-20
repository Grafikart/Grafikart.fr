<script>

  export let folder

  let isSelected = false

  $: hasChildren = folder.children.length > 0

  function onClick (e) {
    e.preventDefault()
    isSelected = !isSelected
  }

</script>

<div class="hierarchy__item"
     class:is-selected={isSelected}
     class:has-children={hasChildren}
     class:is-deeper={!hasChildren} on:click={onClick}>
  <FolderIcon/>
  {folder.folder}
  <span>({folder.count})</span>
</div>
{#if isSelected}
  {#each folder.children as child}
    <svelte:self folder={child}></svelte:self>
  {/each}
{/if}
