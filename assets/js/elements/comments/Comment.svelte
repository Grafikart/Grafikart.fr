<script>
  import Icon from './Icon.svelte'
  import {slide} from 'svelte/transition'
  import {canManage} from '@fn/auth'
  import { tick } from 'svelte'
  import {updateComment} from '../../api/comments'

  export let comment
  export let replyTo
  export let onDelete

  let editor
  let updatedContent
  let canEdit = canManage(comment.userId)

  async function startEdit () {
    if (!canEdit) {
      return false
    }
    updatedContent = comment.content
    comment.editing = true
    await tick()
    editor.focus()
  }

  function cancelEdit () {
    comment.editing = false
  }

  async function confirmEdit () {
    comment.loading = true
    try {
      const updatedComment = await updateComment(comment.id, updatedContent)
      comment.content = updatedComment.content
    } catch  (e) {
      alert(e.detail ? e.detail : e)
    }
    comment.loading = false
    cancelEdit()
  }
</script>

<div class="comment" transition:slide class:is-loading={comment.loading}>
  {#if comment.loading}
    <spinning-dots class="comment__loader"></spinning-dots>
  {/if}
  <img src="{comment.avatar}" alt="" class="comment__avatar">
  <div class="comment__meta">
    <div class="comment__author">{comment.username}</div>
    <div class="comment__actions">
      <span class="comment__date">
        <time-ago time="{comment.createdAt}"></time-ago>
      </span>
      <a href="#c{comment.id}" on:click|preventDefault={() => replyTo(comment)}>
        <Icon name="reply"></Icon>
        Répondre
      </a>
      {#if canEdit}
        <a href="#c{comment.id}" on:click|preventDefault={startEdit}>
          <Icon name="edit"></Icon>
          Editer
        </a>
        <a href="#c{comment.id}" on:click|preventDefault={() => onDelete(comment)}>
          <Icon name="trash"></Icon>
          Supprimer
        </a>
      {/if}
    </div>
  </div>
  <div class="comment__content form-group stack">
    {#if comment.editing}
      <textarea is="textarea-autogrow" bind:value={updatedContent} bind:this={editor}></textarea>
      <button class="btn-primary" on:click|preventDefault={confirmEdit} disabled={comment.loading}>Modifier</button>
      <button class="btn-secondary" on:click={cancelEdit}>Annuler</button>
    {:else}
      <div on:dblclick={startEdit}>{comment.content}</div>
    {/if}
  </div>
  <div class="comment__replies">
    {#if comment.replies}
      {#each comment.replies as reply (reply.id)}
        <svelte:self comment={reply} replyTo={() => replyTo(comment)}
                     onDelete={() => onDelete(reply, comment)}></svelte:self>
      {/each}
    {/if}
    <slot></slot>
  </div>
</div>
