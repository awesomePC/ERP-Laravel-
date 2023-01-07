<tr class="product_row">
    <td>
        {{$variation_name}}
    </td>
    <td>
        <input type="text" class="form-control input_number input_quantity" value="{{@format_quantity($quantity)}}" name="parts[{{$variation_id}}][quantity]" >
        {{$unit}}
    </td>
    <td class="text-center">
        <i class="fas fa-times remove_product_row cursor-pointer" aria-hidden="true"></i>
    </td>
</tr>