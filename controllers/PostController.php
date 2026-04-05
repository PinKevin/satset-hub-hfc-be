<?php

class PostController extends BaseController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthMiddleware();
    }
    
    public function index() {
        $payload = $this->auth->authenticate();
        
        try {
            $posts = Post::with('user')->get();
            
            return $this->success($posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->name,
                        'email' => $post->user->email
                    ],
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at
                ];
            }));
            
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch posts: ' . $e->getMessage());
        }
    }
    
    public function show($id) {
        $payload = $this->auth->authenticate();
        
        try {
            $post = Post::with('user')->find($id);
            
            if (!$post) {
                return $this->notFound('Post not found');
            }
            
            return $this->success([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'email' => $post->user->email
                ],
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ]);
            
        } catch (Exception $e) {
            return $this->serverError('Failed to fetch post: ' . $e->getMessage());
        }
    }
    
    public function store() {
        $payload = $this->auth->authenticate();
        $data = $this->getRequestData();
        
        $validation = $this->validateRequired($data, ['title', 'content']);
        if ($validation) return $validation;
        
        try {
            $post = new Post();
            $post->title = $data['title'];
            $post->content = $data['content'];
            $post->user_id = $payload['user_id'];
            $post->save();
            
            return $this->created([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user_id' => $post->user_id,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ], 'Post created successfully');
            
        } catch (Exception $e) {
            return $this->serverError('Failed to create post: ' . $e->getMessage());
        }
    }
    
    public function update($id) {
        $payload = $this->auth->authenticate();
        
        try {
            $post = Post::find($id);
            
            if (!$post) {
                return $this->notFound('Post not found');
            }
            
            if ($post->user_id !== $payload['user_id']) {
                return $this->forbidden('You can only update your own posts');
            }
            
            $data = $this->getRequestData();
            
            $post->title = $data['title'] ?? $post->title;
            $post->content = $data['content'] ?? $post->content;
            $post->save();
            
            return $this->success([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user_id' => $post->user_id,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at
            ], 'Post updated successfully');
            
        } catch (Exception $e) {
            return $this->serverError('Failed to update post: ' . $e->getMessage());
        }
    }
    
    public function destroy($id) {
        $payload = $this->auth->authenticate();
        
        try {
            $post = Post::find($id);
            
            if (!$post) {
                return $this->notFound('Post not found');
            }
            
            if ($post->user_id !== $payload['user_id']) {
                return $this->forbidden('You can only delete your own posts');
            }
            
            $post->delete();
            
            return $this->success(null, 'Post deleted successfully');
            
        } catch (Exception $e) {
            return $this->serverError('Failed to delete post: ' . $e->getMessage());
        }
    }
}
?>
