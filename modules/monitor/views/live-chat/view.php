<?php 
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;



$abbreviation_name = ArrayHelper::map($productsCategories,'id','abbreviation_name');
//var_dump($productsCategories)
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
                <?php $index = 0; foreach ($productsCategories as $key => $value): ?>
                        <tr>
                            <td rowspan="<?= 1 ?>"><?= $value->abbreviation_name  ?></td>
                            <?php foreach ($value->products as $key => $products) :?>
                                <td rowspan="<?= 1 ?>"><?= $products->name  ?></td>
                                <?php foreach ($products->models as $key => $model) :?>
                                    <td rowspan="<?= 2 ?>"><?= $model->name  ?></td>
                                <?php endforeach; ?>    
                            <?php endforeach; ?>  
                        </tr> 
                <?php endforeach; ?>
                
              </tbody>
            </table>
          </div>
    </div>


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
                <?php foreach ($productsCategories as $key => $category): ?>
                    <tr>
                      <td rowspan=""><?= $category->abbreviation_name  ?></td>
                        <td>
                            <ul>
                            <?php foreach ($category->products as $key => $products) :?>
                                <li><?= $products->name  ?></li>
                            <?php endforeach;  ?>
                            </ul> 
                        </td>
                      <td>Mark</td>
                    </tr>
                <?php endforeach;  ?>
              </tbody>
            </table>
          </div>
    </div>

</div>
