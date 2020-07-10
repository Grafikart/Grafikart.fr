<script>
  import FolderIcon from './FolderIcon.svelte'

  export let folder
  export let onSelect
  export let currentFolder

  let isSelected = false

  $: if (currentFolder) {
    isSelected = currentFolder.path.startsWith(folder.path)
  }
  $: hasChildren = folder.children.length > 0

  function onClick (e) {
    e.preventDefault()
    onSelect(folder)
  }

</script>

<div class="hierarchy__item"
     class:is-selected={isSelected}
     class:has-children={hasChildren}
     class:is-deeper={!hasChildren} on:click={onClick}>
  <FolderIcon/>
  {folder.folder}
  <span>{folder.count}</span>
</div>
{#if isSelected}
  {#each folder.children as child}
    <svelte:self
      folder={child}
      onSelect={onSelect}
      currentFolder={currentFolder}
    />
  {/each}
{/if}
