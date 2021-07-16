<link rel="stylesheet" href="<?= $this->assetUrl('css/front/_partials/chat.css') . '?v=' . rand(); ?>">
<?= $this->section('css'); ?>

<ul id="tabs" hidden>
    <li class="active" data-room="general"></li>
</ul>

<div class="chat px-2 py-2 container border rounded">
    <div class="bg-light border rounded" id="content ">
        <div class="pl-2" id="messages"></div>
        <!-- <div class ="pl-2 b-2"id="writing"></div> -->
    </div>
    <form id="send-chat" class="py-2 form-inline input-chat ">
        <?php if (isset($w_user)) : ?>
            <input type="text" id="uuid" class="" value="<?= $w_user['uuid'] ?>" hidden>
            <input type="text" id="message-chat" class=" col-10 form-control" placeholder="Entrez votre message">
            <button type="button" class="col-1 btn emojichat form-control" data-toggle="emojiPopper" data-target="#message-chat">&#x1F642;</button>
            <button class="send-chat col-1 btn "><i class=" fas fa-paper-plane"></i></button>
        <?php endif; ?>
    </form>
</div>
