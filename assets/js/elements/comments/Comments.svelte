<script>
  import {findAllComments, addComment} from '../../api/comments'
  import CommentForm from './CommentForm.svelte'

  export let target

  let comments = null
  let isLoading = false
  let reply = null

  async function loadComments () {
    try {
      comments = await findAllComments(target)
    } catch (e) {
      alert(e.detail)
    }
  }

  async function onSubmit (data) {
    isLoading = true
    try {
      const comment = await addComment({target: target, ...data})
      comments = [comment, ...comments]
    } catch (e) {
      alert(e.detail)
    }
    isLoading = false
  }

  function replyTo(comment) {
    reply = comment
  }

  loadComments()
</script>

{#if comments === null}
<div class="comments-loader">
  <spinning-dots></spinning-dots>
</div>
{:else}
  <aside class="comment-area">
    <div class="comments__title">{comments.length} Commentaires</div>

    <CommentForm loading={isLoading} onSubmit={onSubmit}></CommentForm>

    <hr>

    <div class="comment-list">
      {#each comments as comment}
      <div class="comment">
        <img src="{comment.avatar}" alt="Avatar {comment.username}" class="comment__avatar">
        <div class="comment__meta">
          <div class="comment__author">{comment.username}</div>
          <div class="comment__actions">
            <a class="comment__date" href="#c{comment.id}">
              <time-ago time="{comment.createdAt}"></time-ago>
            </a>
            <a href="#c{comment.id}" on:click|stopPropagation|preventDefault={() => replyTo(comment)}>Répondre</a>
            <a href="#c{comment.id}">Supprimer</a>
          </div>
        </div>
        <div class="comment__content">{comment.content}</div>
        <div class="comment__replies">
          {#if reply === comment}
            <CommentForm loading={isLoading} onSubmit={onSubmit}></CommentForm>
          {/if}
        </div>
      </div>
      {/each}
    </div>


  </aside>
{/if}
