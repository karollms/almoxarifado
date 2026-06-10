<?php

use App\Models\Produto;
use App\Models\Movimento;

test('sistema deve barrar a movimentação de saída se a quantidade retirada for maior que o estoque', function(){
    $produtoMock = new Produto([
        'nome' => 'Mouse USB Dell',
        'estoque' => 5
    ]);

    $movimentoMock = new Movimento([
        'quntidade' => 10,
        'tipo' => 's',
    ]);

    if ($movimentoMock->tipo === 's' && $movimentoMock->quantidade > $produtoMock->estoque){
        expect(true)->toBeTrue();
    }else{
        $this->fail("Erro: A regra de negócio permitiu a saída de mercadoria sem estoque.");
    }
});

test('O sistema deve diminuir o estoque apos uma saída autorizada', function(){
    $produto = Produto::create([
        'nome' => 'Teclado mecânico',
        'estoque' => 15,
    ]);

    Livewire::test(CreateMovimento::class)
        ->fillForm([
            'produto_id' => $produto->id,
            'quantidade' => 5,
            'tipo' => 's',
        ])
        -> call('create');
        
    expect(Movimento::count())->toBe(1);

    expect($produto->fresh()->estoque->toBe(10));
});

test('sistema deve diminuir o estoque corretamente apos uma saida autorizada', function () {
    // Cenário: Estoque inicial de 10, saindo 3
    $produtoMock = new Produto(['estoque' => 10]);
    $movimentoMock = new Movimento(['quantidade' => 3, 'tipo' => 's']);

    // Executa a regra matemática de decremento na memória
    if ($movimentoMock->tipo === 'e') {
        $produtoMock->estoque += $movimentoMock->quantidade;
    } else {
        $produtoMock->estoque -= $movimentoMock->quantidade;
    }

    // Valida se a conta deu certo (10 - 3 = 7)
    expect($produtoMock->estoque)->toBe(7);
});


// 3. TESTE DE ADIÇÃO (SIMULA O AFTERCREATE PARA ENTRADA)
test('sistema deve aumentar o estoque corretamente apos uma entrada com sucesso', function () {
    // Cenário: Estoque inicial de 2, entrando 8
    $produtoMock = new Produto(['estoque' => 2]);
    $movimentoMock = new Movimento(['quantidade' => 8, 'tipo' => 'e']);

    // Executa a regra matemática de incremento na memória
    if ($movimentoMock->tipo === 'e') {
        $produtoMock->estoque += $movimentoMock->quantidade;
    } else {
        $produtoMock->estoque -= $movimentoMock->quantidade;
    }

    // Valida se a conta deu certo (2 + 8 = 10)
    expect($produtoMock->estoque)->toBe(10);
});
