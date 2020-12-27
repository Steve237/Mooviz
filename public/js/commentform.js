// App
var App = React.createClass({ 
  
    getInitialState: function() {
      return {
        comments: {}
      }
    },
    
    addComment: function(commentData){
      
      var timeStamp = (new Date()).getTime();
      
      this.state.comments['comment-id'+ timeStamp] = commentData;
      
      this.setState({
        comments: this.state.comments
      });
      console.log(commentData);
    },
    
    editComment: function(commentID, editedData){
      
      this.state.comments[commentID].commentBody = editedData;
      this.setState({
        comments: this.state.comments
      });
      
      console.log("Edited text:",editedData);
      
    },
    
    deleteComment: function(commentID){
  
      delete this.state.comments[commentID];
      
      this.setState({
        comments: this.state.comments
      });
      
    },
    
    renderComment: function(key){
      return (
         <li className="">
          <NewComment key={key} index={key} details={this.state.comments[key]} editComment={this.editComment} deleteComment={this.deleteComment}/>
        </li>
      )
    },
    
    render : function() {
      return (
          <div className="row medium-8 large-7 columns">
            <ol className="comment-list block-comments">
              {
                Object
                  .keys(this.state.comments)
                   // Creating a NEW array
                  .map(this.renderComment)
              }
            </ol>
            <AddCommentForm addComment={this.addComment}/>
          
          <pre>{JSON.stringify(this.state, null, 2)}</pre>
          
          </div>
      )
    }
  });
  
  /*
    Add comment Form
    <AddCommentForm />
  */
  var AddCommentForm = React.createClass({
    
    processComment: function(event){
      event.preventDefault();
  
      // 1. Take data from form
      var commentData = {
        commentName: this.refs.name.value,
        commentBody: this.refs.desc.value
      }
  
      // 2. Pass data back to App
      this.props.addComment(commentData);
      
      // 3. Reset the form
      this.refs.commentForm.reset();
      
    },
    
    render : function() {
      return (
        <div className="callout secondary">
          <h4 className="leave-comment">Add a Comment</h4>
          <form className="post-edit" ref="commentForm" onSubmit={this.processComment}>
            <input type="text" ref="name" placeholder="Your Name" required/>
            <textarea ref="desc" placeholder="Add your comment here" required/>
            <button id="submit" type="submit" className="button button-outline comment-button action-button expand-right">Add Comment</button>
          </form>
        </div>
      )
    }
  });
  
  /*
    Newcomment
    <NewComment />
  */
  var NewComment = React.createClass({
    
    getInitialState: function() {
      return {
        editMode: false
      }
    },
    
    renderCommentRead: function(){
     return (
       <div className="comment-text">
            <p>{this.props.details.commentBody}</p>
       </div>
     ) 
    },
    
    editCommentHandler: function(event){
      event.preventDefault();
      
      this.props.editComment(this.props.index, this.refs.editText.value);
          
      this.setState({
        editMode: false
      });
      
    },
    
    deleteCommentHandler: function(){
      
      this.props.deleteComment(this.props.index);
      
    },
    
    renderCommentEdit: function(){
     return (
       <form className="post-edit" ref="commentForm" onSubmit={this.editCommentHandler}>
         <textarea ref="editText" required>{this.props.details.commentBody}</textarea>
         <button id="submit" type="submit" className="button button-outline comment-button action-button expand-right">Done</button>
       </form>
     ) 
    },
    
    enterEditMode: function(){
      if (!this.state.editMode){
        this.setState({
          editMode: true
        });
      }
    },
    
    render : function() {
      return (
        <div className="block-comment-content module text">
          <div className="comment-user">
            <div className="comment-user-avatar-wrap">
              <a>
                <img src="http://spacecentre.co.uk/wp-content/uploads/2017/03/deepspace-image5.jpg" className="comment-avatar" alt=""/>
              </a>
            </div>
            <div className="comment-user-text">
              <a href="#0" data-username="cathbailh" className="comment-username">
                  <span className="username">
                    {this.props.details.commentName}
                  </span>
              </a>
              <span className="on"> on </span>
              <a href="#0">
                <time className="block-comment-time">
                  {h.getTime()}
                </time>
              </a>
              <span className="edit-comment">
                <i className="fa fa-pencil edit-btn" aria-hidden="true" onClick={this.enterEditMode}></i>
                <i className="fa fa-trash-o del-btn" aria-hidden="true" onClick={this.deleteCommentHandler}></i>
              </span>
            </div>
          </div>
          { this.state.editMode ? this.renderCommentEdit() : this.renderCommentRead() }
        </div>
        
      )
    }
  });
  
  ReactDOM.render(<App />, document.querySelector("#main"));