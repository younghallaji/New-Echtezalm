<?php include 'head.php' ?>
<!-- <style>
ul, li .account{
  list-style-image: url('images/icon/user.png');
}
</style> -->
<?php 
if(isset($_GET['id']) && isset($_GET['order'])){
  $prSuID = $_GET['id'];
  $order = $_GET['order'];

  $sub = $connect2db->prepare("SELECT s.order_id,s.user_id,s.sub_date,s.plan,s.sub_total,s.status,s.del_date,coll.Coll_details,coll.info_details,coll.images FROM subscriber as s INNER JOIN collection as coll ON s.plan=coll.id WHERE user_id=? AND order_id=?");
  $sub->execute([$id,$order]);
  $suscrib = $sub->fetch();
  $order_id = $suscrib->order_id;
    $status = $suscrib->status;
    $del = $suscrib->del_date;
    $plan = $suscrib->plan;

  $productID = $_GET['id'];
  $rv = $connect2db->prepare("SELECT * FROM collection WHERE id =?");
  $rv->execute([$productID]);
  $prd = $rv->fetch();

  $d = $connect2db->prepare("SELECT count(id) as id FROM sub_duration WHERE sub_id=? AND user=? AND delv_status=?");
  $d->execute([$order,$id,'Pending']);
  $di = $d->fetch();
  $duration = $di->id;

  $m = $connect2db->prepare("SELECT count(id) as id FROM sub_duration WHERE sub_id=? AND user=?");
  $m->execute([$order,$id]);
  $mi = $m->fetch();
  $monthSub = $mi->id;

// Rating from user
  if(isset($_POST['submit'])){
  $rating = round($_POST['rating']);
  $title = trim($_POST['title']);
  $message = trim($_POST['message']);

  $si = $connect2db->prepare("SELECT user_id, rating, title FROM review WHERE user_id=? AND prd_id=?");
  $si->execute([$id,$prSuID]);
  if($si->rowcount() >0 ){
    echo "<script> alert('Product Already Reviewed by you') </script>";
  }else{
    $status=2;
//    $prSuID==0;
    // echo "<script> alert('".$status."') </script>";
    $ins = $connect2db->prepare("INSERT INTO review(user_id,prd_id,status,rating,title,message)VALUES(?,?,?,?,?,?)");
    $ins->execute([$id,$plan,$status,$rating,$title,$message]);
    if($ins){
      echo "<script> alert('Product Reviewed') </script>";
      }
    }
  }
}



?>
<!-- Description Section Start -->
<section style="padding-top: 0px;background: #0F0F0F;"> <!-- data-aos="flip-up-->
  <div class="container">
    <div class="row"  data-aos="flip-down" data-aos-duration="1000">
<div class="col l4 s12 m4 white-text card" id="black" style="background: #3A3A3A">
        <div class="card" style="padding-left:15px;background:transparent;box-shadow:0px 0px 0px 0px">
            <div class="card-content" style="text-align:left;align-content:left">
              <ul class="side-nav">
              <li class="account active">
                <div class="row">
                  <div class="col s2">
                    <img src="images/icon/user-no.png"style="width:24px;height:24px;margin-top:0px">
                  </div>

                  <div class="col s2">
                    <a href="profile.php" class=" <?php $page=='profile'?print'active':print'white-text';?>" style="margin-top:0px;padding-top:0px;">Account <?php //echo $delDate; ?></a>
                  </div>

                </div>
                
              </li>
              <li>
                <div class="row">
                  <div class="col s2">
                    <img src="images/icon/lock-no.png"style="width:24px;height:24px;margin-top:0px">
                  </div>

                  <div class="col s2">
                    <a href="myOrder.php" class=" <?php $page=='profile'?print'active':print'white-text';?>" style="margin-top:0px;padding-top:0px;">Bestellingen</a>
                  </div>

                </div>
              </li>
              <li>
               <div class="row">
                  <div class="col s2">
                    <img src="images/icon/diamond.png"style="width:24px;height:24px;margin-top:0px">
                  </div>

                  <div class="col s2">
                    <a href="profile-subscription.php" class="active" style="margin-top:0px;padding-top:0px;">Abonnement</a>
                  </div>

                </div>
              </li>
            </ul>
            </div>
            <div class="card-action center">
              <a href="logout.php" class="white-text">Afmelden</a>
            </div>
          </div>

      </div> <!-- left panel -->
      <div class="col l8 m8 s12 right-align">
        <div class="row">
          <div class="col l12"> 
            <div class="card"style="background:transparent;padding:10px;border:1px solid #fff">
              <div class="card-content">
                  <div class="row"><div class="col l12">
                  <h5 class="left-align"> Order Details</h5>
                  <hr>
                  <div class="row">
                    <div class="col l12 m12"><p class="white-text">Order No: <?php echo $order;?></p></div>
                    <div class="col l12 m12"><p class="white-text"><?php echo $monthSub;?> Month Subscription (<?php echo $duration;?> month remaining)</p></div>
                  </div>

                  <div class="row center" style="margin-top:25px;padding-left:0px;">
                    <div class="col l11 m11">
                        <div class="row">
                          <div class="col l4 s12" style="padding-left:0px">
                            <img src="admin/collection/<?php echo $suscrib->images;?>" style="width:130px;height:130px;margin-top:26px" class="responsive-img">
                          </div>
                          <div class="col l8 s12">
                            <div>
                            <p style="margin-bottom:0px;margin-top:30px"><?php echo $suscrib->Coll_details;?> - <?php echo $suscrib->info_details;?></p></div>

                            <div style="margin-top:20px">
                            <p style ="margin-bottom:0px"> Placed on <?php echo $suscrib->sub_date;?></p>
                            <p style="margin-bottom:0px;margin-top:0px;color:#DAC08E"> € <?php echo number_format($suscrib->sub_total,2);?></p> </div>
                          </div>

                            <div class="col s12 center" style="padding-left:20px;margin-top:20px"> <button type="submit" style="width:100%;height: 41px;background:#3A3A3A;color:#7CF094">
                                <?php if($status =='Expired'){
    echo'<span class="red-text">Expired</span>';
}else if($status == 'Cancelled'){
     echo'<span class="red-text">Cancelled</span>';
}else if($status == 'Pending'){
     echo'<span class="grey-text">Pending</span>';
}else{
echo "Next Delivery ".$del;
}?>
                                </button>
                            <?php if($status == 'Expired'){
                  echo '<a href="renew.php?plan='.$plan.'&order_id='.$order_id.'" class="center" style="color:#DAC08E;">Renew</a>';}?>
                            </div>
                            
                        </div>
                    </div>  <!-- Left side pane of the right account panel -->
                  </div>
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row" style="margin-top:0px">
          <div class="col s12"> 
            <div class="card"style="background:transparent;padding:10px;border:1px solid #fff;">
              <div class="card-content">
                  <div class="row"><div class="col s12">
                  <h5 class="left-align"> Delivery Information</h5>
                  <hr>
                  <div class="row">
                    <div class="col l12 m12"><p class="white-text">Shipping Address</p></div>
                  </div>

                  <div class="row center" style="margin-top:25px;padding-left:0px;">
                    <div class="col l11 m11">
                        <div class="row">
                          <div class="col l8 s8">
                            <div style="margin-top:0px">
                            <p style ="margin-bottom:0px"> 
                              <?php 
                                $i = $connect2db->prepare("SELECT user_id, country, state, lga, zip_code,apartment FROM shipping_details WHERE user_id=? and order_id=?");
                                $i->execute([$id,$order]);
                                
                                if($i->rowcount() <1){
                                  echo "Delivery Information not found";
                                }else{$col = $i->fetch();?> 
                                  <?php echo $col->apartment;?> <br>
                                  <?php echo $col->lga;?> <br>
                                  <?php echo $col->state . ''. $col->country;?> <br>

                                <?php } ?>
                            </p>
                            </div>
                          </div>  <!-- Left side pane of the right account panel -->
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- right panel -->
    </div>
        <div class="row" style="margin-top:0px">
          <div class="col s12"> 
            <div class="card"style="background:transparent;padding:10px;border:1px solid #fff;">
              <div class="card-content">
                  <div class="row"><div class="col s12">
                  <h5 class="left-align"> Payment Information</h5>
                  <hr>
                  <div class="row">
                    <div class="col l12 m12"><p class="white-text">Payment Method</p>
                      <p class="white-text">Ideal</p></div>
                  </div>

                  <div class="row center" style="margin-top:25px;padding-left:0px;">
                    <div class="col l11 m11">
                      <h5 class="white-text left-align">Payment Detail</h5>
                        <div class="row">
                          <div class="col m4 s12">
                            <div style="margin-top:0px">
                            <p style ="margin-bottom:0px"> Product Total:</p>
                            <p style ="margin-bottom:0px"> Shipping Fee:</p>
                            <p style ="margin-bottom:0px"> Total:</p>
                            </div>
                          </div>  <!-- Left side pane of the right account panel -->
                          <div class="col m8 s12">
                            <p style="margin-bottom:0px;margin-top:0px;color:#DAC08E"> €<?php echo number_format($suscrib->sub_total, 2);?></p>
                            <p style="margin-bottom:0px;margin-top:0px;color:#DAC08E"> €1,000.00</p>
                            <p style="margin-bottom:0px;margin-top:0px;color:#DAC08E"> €<?php echo number_format($suscrib->sub_total+1000, 2);?></p>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- right panel -->
    </div>
<?php 
  $r = $connect2db->prepare("SELECT user_id, rating, title,message FROM review WHERE user_id=? AND prd_id=? AND status = ?");
  $r->execute([$id,$plan,2]);
  if($r->rowcount() >0 ){$ri = $r->fetch();?>
        <div class="row" style="margin-top:0px">
          <div class="col s12"> 
            <div class="card"style="background:transparent;padding:10px;border:1px solid #fff;">
              <div class="card-content">
                  <div class="row"><div class="col s12">
                  <h5 class="left-align" style="margin-top:0px"> Your Review</h5>
                  <hr>
                  <div class="row" style="padding-left:15px">
                     <div class="left-align">
                      <?php
                      for($i=1;$i<=$ri->rating;$i++){
                          echo "<img src='images/icon/star.png' style='margin-top:7px'>";
                        }
                      ?>
                        
                        <!-- <img src="images/icon/star.png">
                        <img src="images/icon/star.png">
                        <img src="images/icon/star.png">
                        <img src="images/icon/star.png"> -->
                      </div>
                    <p class="white-text"> <?php echo $ri->title;?></p> <br>
                    <p class="white-text" style ="margin-bottom:0px;width:85%"><?php echo $ri->message; ?></p>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- right panel -->
    </div>

  <?php
  }else{ ?>

    
        <div class="row" style="margin-top:0px">
          <div class="col l12" style="margin-bottom:0px;padding-bottom:0px"> 
            <div class="card"style="background:transparent;padding:10px;border:1px solid #fff">
              <div class="card-content">
                   <div class="row"><div class="col l12">
                  <h5 class="left-align"> Schrijf een recensie</h5>
                  <hr><p class="white-text">Beoordeel onze service</p>
                  <form method="POST" action="">
                    <div class="rateyo" id= "rating" data-rateyo-rating="0" data-rateyo-read-only="false"></div>
                    <br>
                         <span class='result'>0</span>
                        <input type="hidden" name="rating">
                    </div>
                  </div>

                  <div class="row center" style="margin-top:0px;padding-left:0px;padding-top:0px">
                    <div class="col l11 m11">
                        <div class="row">
                          <div class="col l12 s12" style="margin-top:0px;padding-top:0px">
                            <div style="margin-top:0px">
                              <div class="row">
                                <div class="col s12 input-field">
                                  <label for="title" style="padding-left: 20px;">Title <span class="red-text">*</span> </label>
                                  <input type="text" id="title" class="browser-default input" name="title">
                                </div>
                                <div class="col s12 input-field">
                                  <label for="phone" style="padding-left: 20px;">Message <span class="red-text">*</span> </label>
                                  <textarea class="browser-default input" style="height: 111px;" name="message"></textarea>
                                </div>
                                <div class="col s12 input-field">
                                    <!-- <input type="submit" class="btn planbtn" name=""> -->
                                    <button class="btn planbtn right" type="submit" name="submit">Submit</button>
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
            </form>


            </div>
          </div>
        </div>

        <?php } ?>
      </div> <!-- right panel -->
    </div>
  </div>
</section>
<!-- Description Section End -->
<!-- <img src="images/icon/user.png"style="width:24px;height:24px"> -->
<?php include ('footer.php'); ?>

<script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<!-- Animation on Scroll -->
<script type="text/javascript" src="js/aos.min.js"></script>
<!-- Flip Card -->
<script type="text/javascript" src="js/flip.min.js"></script>
<!-- rating animation script -->
<script src="js/rateyo.min.js"></script>

<script type="text/javascript">
	AOS.init();


</script>
<script>
 
 
    $(function () {
        $(".rateyo").rateYo().on("rateyo.change", function (e, data) {
            var rating = data.rating;
            $(this).parent().find('.score').text('score :'+ $(this).attr('data-rateyo-score'));
            $(this).parent().find('.result').text('rating :'+ rating);
            $(this).parent().find('input[name=rating]').val(rating); //add rating value to input field
        });
    });
 
</script>
<?php include 'script.php' ?>