<script>
  import {findAllComments, addComment, deleteComment} from '../../api/comments'
  import CommentForm from './CommentForm.svelte'
  import Comment from './Comment.svelte'

  export let target

  let comments = null // Liste des commentaires
  let isLoading = false // Chargement de la liste des commentaires
  let reply = null // Commentaire sur lequel on est en train de répondre
  let count = 0 // Nombre total de commentaires

  async function loadComments () {
    try {
      const {comments:c, count: total} = await findAllComments(target)
      count = total
      comments = c.sort((a,b) => b.createdAt - a.createdAt)
    } catch (e) {
      alert(e.detail)
    }
  }

  async function onSubmit (data, parent = null) {
    isLoading = true
    try {
      const comment = await addComment({target: target, ...data})
      if (parent === null) {
        comments = [comment, ...comments]
      } else {
        parent.replies = [...parent.replies, comment]
        comments = comments // force svelte à voir le changement
      }
      count++
      isLoading = false
      reply = null
    } catch (e) {
      isLoading = false
      throw e
    }
  }

  /**
   * @param {CommentResource} comment
   * @return {Promise<void>}
   */
  async function onDelete (comment, parent = null) {
    comment.loading = true
    comments = comments // Force svelte à tracker le changement
    try {
      await deleteComment(comment.id)
      if (parent === null) {
        comments = comments.filter(c => c !== comment)
      } else {
        parent.replies = parent.replies.filter(c => c !== comment)
        comments = comments // force svelte à voir le changement
      }
      count--
    } catch (e) {
      alert(e.detail)
    }
  }

  function scrollTo(node) {
    return {
      duration: 500,
      tick: t => {
        if (t === 1) {
          window.scrollTo({
            top: node.getBoundingClientRect().top,
            left: 0,
            behavior: 'smooth'
          })
        }
      }
    };
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
    <div class="comments__title">{count} Commentaires</div>

    <CommentForm loading={isLoading} onSubmit={onSubmit}></CommentForm>

    <hr>

    <div class="comment-list">
      {#each comments as comment (comment.id)}
      <Comment comment={comment} replyTo={replyTo} onDelete={onDelete}>
        {#if reply === comment}
          <CommentForm loading={isLoading} onSubmit={(data) => onSubmit(data, reply)} scroll={true}></CommentForm>
        {/if}
      </Comment>
      {/each}
    </div>


  </aside>
{/if}
