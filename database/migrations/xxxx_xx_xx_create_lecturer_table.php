use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLecturerTable extends Migration
{
    public function up()
    {
        Schema::create('lecturer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->string('name');
            $table->string('staff_id')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('research_group')->nullable();
            $table->integer('max_students')->default(5);
            $table->integer('current_students')->default(0);
            $table->boolean('accepting_students')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lecturer');
    }
} 