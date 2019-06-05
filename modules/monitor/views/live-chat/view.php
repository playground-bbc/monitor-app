<?php 
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\Products;
use app\models\ProductsCategories;
use app\models\ProductsModels;



$abbreviation_name = ArrayHelper::map($productsCategories,'id','abbreviation_name');

$modelME = ProductsCategories::find()->where(['abbreviation_name' => 'HE'])->with('products')->all();
$modelMA = ProductsCategories::find()->where(['abbreviation_name' => 'HA'])->with('products')->all();
$modelMC = ProductsCategories::find()->where(['abbreviation_name' => 'MC'])->with('products')->all();
$modelMP = ProductsCategories::find()->where(['abbreviation_name' => 'MP'])->with('products')->all();
/*foreach ($modelMC as $key => $value) {
  echo($value->products);
}*/

 ?>
<div class="monitor-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>
    <p>
        This is the view content for action "<?= $this->context->action->id ?>".
        The action belongs to the controller "<?= get_class($this->context) ?>"
        in the "<?= $this->context->module->id ?>" module.
    </p>
    <p>
        You may customize this page by editing the following file:<br>
        <code><?= __FILE__ ?></code>
    </p>
    <div class="container">
        <p><h2>Lineas de Productos</h2></p>            
        <div class="bs-docs-example">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Categoria</th>
                  <th>Productos</th>
                  <th>Modelos</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <?php foreach ($modelME as $key => $value): ?>
                    <td><?= $value->name  ?></td>
                    <td>
                      <?php foreach ($value->products as $key => $product): $id[] = $product->id?>
                        <li><?= $product->name  ?></li>        
                      <?php endforeach; ?>
                    </td>
                    <td>
                      <?php $models = ProductsModels::find()->where(['productId' => $id])->all(); ?>
                      <?php foreach ($models as $key => $model):?>
                        <li><?= $model->name  ?></li>        
                      <?php endforeach; ?>
                    </td>  
                  <?php endforeach; ?>
                </tr>
                <tr>
                  <?php foreach ($modelMA as $key => $value): ?>
                    <td><?= $value->name  ?></td>
                    <td>
                      <?php foreach ($value->products as $key => $product): $idMA[] = $product->id?>
                        <li><?= $product->name  ?></li>        
                      <?php endforeach; ?>
                    </td>
                    <td>
                      <?php $models = ProductsModels::find()->where(['productId' => $idMA])->all(); ?>
                      <?php foreach ($models as $key => $model):?>
                        <li><?= $model->name  ?></li>        
                      <?php endforeach; ?>
                    </td>  
                  <?php endforeach; ?>
                </tr>
                <tr>
                  <?php foreach ($modelMC as $key => $value): ?>
                    <td><?= $value->name  ?></td>
                    <td>
                      <?php foreach ($value->products as $key => $product): $idMC[] = $product->id?>
                        <li><?= $product->name  ?></li>        
                      <?php endforeach; ?>
                    </td>
                    <td>
                      <?php $models = ProductsModels::find()->where(['productId' => $idMC])->all(); ?>
                      <?php foreach ($models as $key => $model):?>
                        <li><?= $model->name  ?></li>        
                      <?php endforeach; ?>
                    </td>  
                  <?php endforeach; ?>
                </tr>
                <tr>
                  <?php foreach ($modelMP as $key => $value): ?>
                    <td><?= $value->name  ?></td>
                    <td>
                      <?php foreach ($value->products as $key => $product): $idMP[] = $product->id?>
                        <li><?= $product->name  ?></li>        
                      <?php endforeach; ?>
                    </td>
                    <td>
                      <?php $models = ProductsModels::find()->where(['productId' => $idMP])->all(); ?>
                      <?php foreach ($models as $key => $model):?>
                        <li><?= $model->name  ?></li>        
                      <?php endforeach; ?>
                    </td>  
                  <?php endforeach; ?>
                </tr>
                
              </tbody>
            </table>

          </div>
          <p><h2>Lineas de Productos por Palabras</h2></p>
          <div class="bs-docs-example">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Username</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Mark</td>
                  <td>Otto</td>
                  <td>@mdo</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Jacob</td>
                  <td>Thornton</td>
                  <td>@fat</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Larry</td>
                  <td>the Bird</td>
                  <td>@twitter</td>
                </tr>
              </tbody>
            </table>
          </div>  
    </div>
</div>
