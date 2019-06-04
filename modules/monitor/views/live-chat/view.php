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
                            <td rowspan="<?= $index *= 4 ?>"><?= $value->abbreviation_name  ?></td>
                            <?php foreach ($value->products as $key => $products) :?>
                                <td><?= $products->name  ?></td>
                                <?php foreach ($products->models as $key => $model) :?>
                                    <td><?= $model->name  ?></td>
                                <?php endforeach; ?>    
                            <?php endforeach; ?>  
                        </tr> 
                <?php endforeach; ?>
                
              </tbody>
            </table>
          </div>
    </div>

</div>
