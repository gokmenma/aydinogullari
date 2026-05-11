<div class="content pd-20 bg-white border-radius-16 box-shadow mb-10">
    <div class="clearfix mb-20">
        <div class="pull-left">
            <h5 class="text-blue">Sürüm notları</h5>
        </div>

    </div>
</div>

<style>
    .list-group {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .list-group-item {
        border: 1px solid #5f5f5f;
        margin-top: -1px;
        padding: 12px;
        background-color: #3a3a3a;
        color: #ffffff;
        /* Optional: to make text readable on dark background */
    }

    .list-group-item:hover {
        background-color: #303030;
        cursor: pointer;
    }

    .list-group-item:first-child {
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }

    .list-group-item:last-child {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>

<div class="content pd-20 bg-white border-radius-16 box-shadow mb-20">
    <div class="clearfix mb-20">
        <div class="pull-left"></div>
        <ul class="list-group">


            <?php
            // Sürüm notları buraya eklenecek

            $sql = $ac->prepare("SELECT * from version_notes order by id desc");
            $sql->execute();
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) { ?>


                <li class="list-group-item">
                    <strong><?php echo $row->title ?></strong>
                    <p>
                    <?php echo $row->description ?>
                    </p>
                    <p>
                        <small><?php echo $row->created_at ?></small>
                    </p>
                </li>

            <?php  } ?>





        </ul>
        </ul>
    </div>
</div>