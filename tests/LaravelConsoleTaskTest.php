<?php

declare(strict_types=1);

/**
 * This file is part of Laravel Console Task.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Tests\LaravelConsoleTask;

use ReflectionClass;
use Illuminate\Console\Command;
use PHPUnit\Framework\TestCase;
use Illuminate\Console\OutputStyle;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;

/**
 * This is the service provider test class.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class LaravelConsoleTaskTest extends TestCase
{
    public function testSuccessfulTaskWithReturnValueAndDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithDecoratedOutput(
            function () {
                return true;
            }
        );
    }

    public function testSuccessfulTaskWithoutReturnValueAndDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithDecoratedOutput(
            function () {
            }
        );
    }

    private function performTestSuccessfulTaskWithDecoratedOutput(callable $task)
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Foo: <comment>loading...</comment>')],
                [$this->equalTo("\x0D")],
                [$this->equalTo("\x1B[2K")]
            );

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Foo: <info>✔</info>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', $task)
        );
    }

    public function testSuccessfulTaskWithAdditionalDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(5))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Foo: <comment>loading...</comment>')],
                [$this->equalTo("\x1B[s")],
                [$this->equalTo("\x1B[7A")],
                [$this->equalTo("\x1B[2K")],
                [$this->equalTo("\x1B[u")]
            );

        $outputMock->expects($this->once())
            ->method('newLine');

        $outputMock->expects($this->exactly(7))
            ->method('writeln')
            ->withConsecutive(
                ['  -> Foo</>'],
                ['  -> <info>Bar</>'],
                ['  -> <error>Baz</>'],
                ['  -> <fg=magenta>Fizz</>'],
                ['  -> <fg=blue>Buzz</>'],
                ['  -> <comment>Lightning</>'],
                ['Foo: <info>✔</info>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', function ($output) {
                $output->text('Foo');
                $output->success('Bar');
                $output->error('Baz');
                $output->note('Fizz');
                $output->caution('Buzz');
                $output->comment('Lightning');
            })
        );
    }

    public function testSuccessfulTaskWithReturnValueAndWithoutDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithoutDecoratedOutput(
            function () {
                return true;
            }
        );
    }

    public function testSuccessfulTaskWithoutReturnValueAndWithoutDecoratedOutput()
    {
        $this->performTestSuccessfulTaskWithoutDecoratedOutput(
            function () {
            }
        );
    }

    private function performTestSuccessfulTaskWithoutDecoratedOutput(callable $task)
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Foo: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Foo: <info>✔</info>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', $task)
        );
    }

    public function testSuccessfulTaskWithAdditionalOutputButWithoutDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Foo: <comment>loading...</comment>');

        $outputMock->expects($this->once())
            ->method('newLine');

        $outputMock->expects($this->exactly(7))
            ->method('writeln')
            ->withConsecutive(
                ['  -> Foo</>'],
                ['  -> <info>Bar</>'],
                ['  -> <error>Baz</>'],
                ['  -> <fg=magenta>Fizz</>'],
                ['  -> <fg=blue>Buzz</>'],
                ['  -> <comment>Lightning</>'],
                ['Foo: <info>✔</info>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertTrue(
            $command->task('Foo', function ($output) {
                $output->text('Foo');
                $output->success('Bar');
                $output->error('Baz');
                $output->note('Fizz');
                $output->caution('Buzz');
                $output->comment('Lightning');
            })
        );
    }

    public function testUnsuccessfulTaskWithDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(true);

        $outputMock->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                [$this->equalTo('Bar: <comment>loading...</comment>')],
                [$this->equalTo("\x0D")],
                [$this->equalTo("\x1B[2K")]
            );

        $outputMock->expects($this->once())
            ->method('writeln')
            ->with('Bar: <error>failed</error>');

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                }
            )
        );
    }

    public function testUnsuccessfulTaskWithoutDecoratedOutput()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>failed</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->assertFalse(
            $command->task(
                'Bar',
                function () {
                    return false;
                }
            )
        );
    }

    public function testUnsuccessfulTaskWithException()
    {
        $command = new Command();

        $outputMock = $this->createMock(OutputStyle::class);

        $outputMock->expects($this->atLeastOnce())
            ->method('isDecorated')
            ->willReturn(false);

        $outputMock->expects($this->once())
            ->method('write')
            ->with('Bar: <comment>loading...</comment>');

        $outputMock->expects($this->exactly(2))
            ->method('writeln')
            ->withConsecutive(
                [''],
                ['Bar: <error>failed</error>']
            );

        $commandReflection = new ReflectionClass($command);

        $commandOutputProperty = $commandReflection->getProperty('output');
        $commandOutputProperty->setAccessible(true);
        $commandOutputProperty->setValue($command, $outputMock);

        (new LaravelConsoleTaskServiceProvider(null))->boot();

        $this->expectException(\Exception::class);

        $command->task(
            'Bar',
            function () {
                throw new \Exception();
            }
        );
    }
}
