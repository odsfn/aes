<?php

/*
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
$this->breadcrumbs->add('Messages', '/messaging/index');
?>

<ul class="nav nav-tabs">
    <li class="active"><a href="#sys" data-toggle="tab">System messages <span class="text-warning" title="You got 4 new">(+4)</span></a></li>
    <li><a href="#conversations" data-toggle="tab">Conversations <span class="text-warning" title="You got 2 new">(+2)</span></a></li>
    <li><a href="#active" data-toggle="tab">View conversation <span class="text-warning" title="You got 1 new">(+1)</span></a></li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane active" id="sys"><h1>System messages</h1></div>
    
    <div class="tab-pane" id="conversations">
        
        <div class="navbar head">
            <div class="navbar-inner">
                <form class="navbar-search pull-left span5">
                    <input type="text" class="search-query span12" placeholder="Type name, email of companion or theme of conversation">
                </form>
                
                <ul class="nav pull-right">
                    <li><a>Found 4 conversations</a></li>
                </ul>
            </div>
        </div>
        
        <div id="conversations">
        
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">John Lennon</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM at "Conversation 1"</a></small>
                            <i class="icon-bell pull-right visible"></i>
                        </h5>

                        <div class="post-content">
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.
                        </div>

                    </div>
                </div>
            </div>

            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">John Doe</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right visible"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">Steve Jobs</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM at "Conversation 3"</a></small>
                            <i class="icon-bell pull-right"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">Andrew Stuart Tanenbaum</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>             
            
        </div><!-- #conversations --> 
        
        <div id="posts-load-btn">
            <div>
                <div class="row-fluid get-more">
                    <div class="span12"><a>More</a><span>Loading...</span></div>
                </div>
            </div>
        </div>
        
    </div><!-- #conversations -->
    
    <div class="tab-pane" id="active">
        
        <div class="navbar head">
            <div class="navbar-inner">
                <form class="navbar-search pull-left span5">
                    <input type="text" class="search-query span12" placeholder="Conversation from 2013/09/24 (default theme)">
                </form>
                
                <ul class="nav pull-right">
                    <li><a>Total Messages Count: 40</a></li>
                    <li><button type="more" class="btn">More</button></li>
                </ul>
            </div>
        </div>
        
        <div id="messages">
        
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">John Lennon</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right"></i>
                        </h5>

                        <div class="post-content">
                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.
                        </div>

                    </div>
                </div>
            </div>

            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">John Doe</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">Steve Jobs</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>
            
            <div class="media post">
                <div class="pull-left">
                    <div class="img-wrapper-tocenter users-photo users-photo-3">
                        <span></span>
                        <a name="5" href="http://aes.dev/userPage/3"><img src="" alt="Jhon Lenon"></a>
                    </div>
                </div>

                <div class="media-body">
                    <div class="post-body">
                        <h5 class="media-heading">
                            <span class="user">Andrew Stuart Tanenbaum</span> 
                            <small><a href="#5">Aug 14, 2013 11:42:00 AM</a></small>
                            <i class="icon-bell pull-right visible"></i>
                        </h5>

                        <div class="post-content">
                            The collection's comparator may be included as an option. If you define an initialize function, it will be invoked when the collection is created.
                        </div>

                    </div>
                </div>
            </div>             
            
        </div><!-- #messages --> 
        
        <div class="new-post row-fluid">
            <div class="body span10 well">
                <textarea class="span12"></textarea>
                <div class="controls pull-right">
                    <button data-loading-text="Sending..." class="btn btn-primary post" type="button">Send</button>
                    <button class="btn" type="button">Attach</button>
                </div>
            </div>
            
            <div class="participant">
                <div class="img-wrapper-tocenter users-photo"><span></span><img alt="Another User" src="http://aes.dev/uploads/photos/unknown_user_96x96.png"></div>
            </div>
        </div>
        
    </div><!-- active -->
    
</div>